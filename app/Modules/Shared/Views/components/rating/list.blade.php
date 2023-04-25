@if($ratings->count())
    <div class="table">
        <div class="table-head table-row hidden-sm-down">
            <div class="table-cell col-md-1">
                Betyg
            </div>
            <div class="table-cell col-md-3">
                Trafikskola
            </div>
            <div class="table-cell col-md-2">
                Betygsättare
            </div>
            <div class="table-cell col-md-3">
                Datum
            </div>
            @if (auth()->user()->isAdmin())
            <div class="table-cell col-md-3">
                Action
            </div>
            @endif
        </div>
        @foreach($ratings as $rating)
            <div class="table-row">
                <div class="table-cell col-md-1">
                    <span class="school-rating">{{ $rating->rating }}</span>
                </div>
                <div class="table-cell col-md-3">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin::schools.show', ['id' => $rating->school_id]) : route('organization::schools.show', ['id' => $rating->school_id]) }}">
                        @if(is_null($rating->school))
                            <strike>School Deleted</strike>
                        @else
                            {{ $rating->school->name }}, {{ $rating->school->city->name }}
                        @endif
                    </a>
                </div>
                <div class="table-cell col-md-2">
                    @if(is_null($rating->user))
                        <strike>Deleted</strike>
                    @else
                        {{ $rating->user->name }}
                    @endif
                </div>
                <div class="table-cell col-md-3">
                    {{ $rating->created_at }}
                </div>
                @if (auth()->user()->isAdmin())
                <div class="table-cell col-md-3">
                    <a href="{{ route('admin::ratings.edit', $rating->id  ) }}" class="bnt btn-primary btn-sm">Redigera</a>
                    <a href="{{ route('admin::ratings.delete', $rating->id  ) }}" class="bnt btn-danger btn-sm">Radera</a>
                    @if($rating->verified)
                        @icon('star')
                    @endif
                </div>
                @endif
            </div>
        @endforeach
    </div>
@else
    <no-results title="Ni har inga betyg ännu"></no-results>
@endif
