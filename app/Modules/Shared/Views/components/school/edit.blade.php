@set('prefix', auth()->user()->isAdmin() ? 'admin' : 'organization')

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    var sortableData = [];
    var schoolId = {{ $school->id }};

    $(document).on('click','.save-school-prices-btn',function (){
        $(this).css("pointer-events", "none");
    })

    $(document).on('change', '.current-school-vehicle-filter', function () {
        var selectedVehicle = $(this).find("option:selected").val();
        var url = '';
        if (selectedVehicle != '') {
            url = "{{ route(auth()->user()->isOrganizationUser() ? 'organization::schools.show' : 'admin::schools.show', $school->id) }}" + "?" + new URLSearchParams({vehicle_segment_id: selectedVehicle}).toString() + '#courses';
        } else {
            url = "{{ route(auth()->user()->isOrganizationUser() ? 'organization::schools.show' : 'admin::schools.show', $school->id) }}" + '#courses'
        }

        window.history.pushState({}, '', url);
        location.reload();

    })

    function saveOrder(vehicle,elem) {
        $(elem).css("pointer-events", "none");
        $.post("{{ route('api::schools.save.order') }}", {
            order: sortableData[vehicle],
            school_id: schoolId,
            "_token": "{{ csrf_token() }}",
            "type": vehicle
        })
            .success(function (data) {
                document.location.reload();
            })
            .error(function (data) {
                document.location.reload();
            });
    }

    $( function() {
        $( ".sortable" ).sortable({
            axis: 'y',
            update: function (event, ui) {
                sortableData[$(this).data('vehicle')] = $(this).sortable('serialize');
            }
        });
        $( ".sortable" ).disableSelection();
    });
</script>

<div class="tab-pane active" id="tab-info" role="tabpanel">
    <form method="POST" action="{{ route($prefix . '::schools.update.details', ['id' => $school->id]) }}" enctype="multipart/form-data">
        {{ csrf_field() }}

        @if(auth()->user()->isAdmin())
            @if($claims->count())
                <div class="jumbotron">
                    @if($claims->count() > 1)
                        <h3>Flera organisationer som gjort anspråk på denna trafikskola:</h3>
                        <ul>
                            @foreach($claims as $claim)
                                <li>
                                    <a href="{{ route('admin::organizations.show', ['id' => $claim->organization->id]) }}">{{ $claim->organization->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <p><strong>Välj en organisation i menyn nedan</strong></p>
                    @else
                        <h3>
                            <a href="{{ route('admin::organizations.show', ['id' => $claims{0}->organization->id]) }}">{{ $claims{0}->organization->name }}</a>
                            har gjort anspråk på denna trafikskola</h3>
                        <p>Bekräfta denna organisation genom att klicka bekräfta, eller välj en annan organisation i
                            menyn nedan</p>
                        <p>
                            <button type="submit" class="btn btn-sm btn-success">Bekräfta
                                <i>{{ $claims{0}->organization->name }}</i></button>
                        </p>
                        <hr>
                    @endif
                    @endif

                    <div class="form-group">
                        <label for="organization_id">Organisation</label>
                        <div class="form-inline">
                            <select class="custom-select" style="display: inline-block" id="organization-id"
                                    name="organization_id">
                                @if($claims->count())
                                    <optgroup label="Organisationer som gjort anspråk">
                                        @foreach($claims as $claim)
                                            <option value="{{ $claim->organization_id }}"
                                                    @if(old('organization_id', $school->organization_id) == $claim->organization_id) selected @endif>{{ $claim->organization->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Övriga organisationer">
                                        @foreach($organizations as $organization)
                                            <option value="{{ $organization->id }}"
                                                    @if(old('organization_id', $school->organization_id) == $organization->id) selected @endif>{{ $organization->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    <option value="">--- Ingen organisation ---</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}"
                                                @if(old('organization_id', $school->organization_id) == $organization->id) selected @endif>{{ $organization->name }}</option>
                                    @endforeach
                                @endif
                            </select>

                            <label class="custom-control custom-checkbox" style="margin:0 0 0 20px">
                                <input class="custom-control-input" type="checkbox" name="not_member"
                                       @if($school->not_member) checked @endif>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description text">Ej medlem</span>
                            </label>

                            <label class="custom-control custom-checkbox" style="margin:0 0 0 20px">
                                <input class="custom-control-input" type="checkbox" name="top_deal"
                                       @if($school->top_deal) checked @endif>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description text">Bästsäljare</span>
                            </label>

                            <label class="custom-control custom-checkbox" style="margin:0 0 0 20px">
                                <input class="custom-control-input" type="checkbox" name="show_left_seats"
                                       @if($school->show_left_seats) checked @endif>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description text">Visa antal platser</span>
                            </label>

                        </div>
                    </div>

                    @if($claims->count())
                        <button type="submit" class="btn btn-sm btn-success">Spara</button>
                </div>
            @endif
        @endif

        <school-edit @if(auth()->user()->isAdmin()) :is-admin="true" @endif  :old-data="{{ json_encode(old()) }}" :initial-school="{{ json_encode($school) }}"></school-edit>
        @include('shared::components.upload.logo', ['entity' => $school])

        @if(!$school->deleted_at)
            <button type="submit" class="btn btn-success">Spara</button>
        @endif
    </form>
</div>

<div class="tab-pane" id="tab-prices" role="tabpanel">
    <form method="POST" action="{{ route($prefix . '::schools.update.prices', ['id' => $school->id]) }}#prices"
          enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="row">
            <div class="col-lg-6">
                <h3>Tilläggsprodukter -
                    <small>Lägg till produkter och sälj dem tillsammans med dina kurser via Körkortsjakten</small>
                </h3>

                <span data-vehicle="addons" class="sortable">
                    @foreach($addons as $addon)
                        <div id="segment-{{ $addon['id'] }}" class="form-group @if($errors->has('addons.' . $addon['id'] . '.id') || $errors->has('addons.' . $addon['id']. '.price')) has-error @endif mb-0">
                        <label class="custom-control custom-checkbox" style="display: block;">
                            <input class="custom-control-input" rel="Addons" type="checkbox"
                                   name="addons[{{$addon['id']}}][id]" value="{{ $addon['id'] }}"
                                   @if(old('addons.' . $addon['id'] . '.id',$school->addons->where('id', $addon['id'])->first())) checked="checked" @endif>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description text"><strong>{{ $addon['name'] }}</strong></span>
                        </label>
                        <div class="input-toggle-content"
                             @if(!old('addons.' . $addon['id'] . '.id',$school->addons->where('id', $addon['id'])->first())) style="display: none"
                             @endif id="input-addon-id-{{ $addon['id'] }}">
                            <div>
                                <label class="form-control-label">Pris:</label>
                                <input class="form-control form-control-sm" type="number"
                                       name="addons[{{$addon['id']}}][price]" min="0"
                                       @if(old('addons.' . $addon['id'] . '.price', $school->addons->where('id', $addon['id'])->first()))
                                       value="{{old('addons.' . $addon['id'] . '.price', $school->addons->where('id', $addon['id'])->first() ? $school->addons->where('id', $addon['id'])->first()->pivot->price : '')}}"
                                        @endif
                                >
                            </div>
                            <div>
                                <label class="form-control-label">Beskrivning</label>
                                <textarea name="addons[{{$addon['id']}}][description]" maxlength="350"
                                          class="form-control">
                                    @if(old('addons.' . $addon['id'] . '.description', $school->addons->where('id', $addon['id'])->first()))
                                        {{old('addons.' . $addon['id'] . '.description', $school->addons->where('id', $addon['id'])->first() ? $school->addons->where('id', $addon['id'])->first()->pivot->description : '')}}
                                    @endif
                                </textarea>
                            </div>
                            @if(auth()->user()->isAdmin())
                                <div>
                                    <label class="custom-control custom-checkbox" style="margin:0 0 0 20px">
                                        <input class="custom-control-input" type="checkbox"
                                               name="addons[{{$addon['id']}}][top_deal]"
                                               @if(old('addons.' . $addon['id'] . '.top_deal', $school->addons->where('id', $addon['id'])->first() ? $school->addons->where('id', $addon['id'])->first()->pivot->top_deal : ''))
                                               checked
                                                @endif
                                        >
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description text">Bästsäljare</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </span>
                <div class="align-right">
                    <span class="btn btn-success save-school-prices-btn"
                          onclick='saveOrder("addons", this)'>Uppdatera order</span>
                </div>
                <hr>
                <h3>Anpassade produkter/paket</h3>
                @if(count($customAddons))

                    @php($customAddons = $customAddons->sortBy('sort_order')->values())
                    <span data-vehicle="customAddons" class="sortable">
                        @foreach($customAddons as $customAddon)
                            <div class="form-group mb-0" id="segment-{{ $customAddon->id }}" >
                                <div class="d-flex">
                                    <label class="custom-control custom-checkbox" style="display: block;">
                                        <input class="custom-control-input" rel="customAddons" type="checkbox"
                                               name="customAddons[{{$customAddon->id}}][id]"
                                               value="{{ $customAddon->id }}"
                                               @if($customAddon->active) checked="checked" @endif readonly>
                                        <span class="custom-control-indicator"></span>
                                        <span
                                            class="custom-control-description text"><strong>{{ $customAddon->name }}</strong></span>
                                    </label>
                                    <a class="btn btn-sm btn-danger float-xs-right"
                                    href="{{ route($prefix . '::schools.delete.customAddon', ['id' => $customAddon->id]) }}">
                                        Ta bort
                                    </a>
                                </div>
                                <div class="input-toggle-content" @if(!$customAddon->active) style="display: none;"
                                     @endif id="input-customAddons-id-{{ $customAddon->id }}">
                                    <div>
                                        <label class="form-control-label">Pris:</label>
                                        <input class="form-control form-control-sm" type="number"
                                               name="customAddons[{{$customAddon->id}}][price]" min="0"
                                               value="{{ $customAddon->price }}">
                                    </div>
                                    <div>
                                        <label class="form-control-label">Beskrivning</label>
                                        <textarea maxlength="350" name="customAddons[{{$customAddon->id}}][description]"
                                                  class="form-control">{!! $customAddon->description !!}</textarea>
                                    </div>
                                    <div>
                                        <label class="custom-control custom-checkbox" style="margin:0 0 0 20px">
                                            <input class="custom-control-input" type="checkbox"
                                                   name="customAddons[{{$customAddon->id}}][show_left_seats]"
                                                   @if($customAddon->show_left_seats) checked @endif>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description text">Visa antal platser</span>
                                        </label>
                                    </div>
                                    @if(auth()->user()->isAdmin())
                                        <div>
                                            <label class="form-control-label">Left seats:</label>
                                            <input class="form-control form-control-sm" type="number"
                                                   name="customAddons[{{$customAddon->id}}][left_seats]" min="0"
                                                   value="{{ $customAddon->left_seats }}">
                                        </div>
                                        <div>
                                            <label class="custom-control custom-checkbox" style="margin:0 0 0 20px">
                                                <input class="custom-control-input" type="checkbox"
                                                       name="customAddons[{{$customAddon->id}}][top_deal]"
                                                       @if($customAddon->top_deal) checked @endif>
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description text">Bästsäljare</span>
                                            </label>
                                        </div>
                                    @endif
                                    <hr>
                                </div>
                            </div>
                        @endforeach
                    </span>
                    <div class="align-right">
                        <span class="btn btn-success save-school-prices-btn" onclick='saveOrder("customAddons", this)'>Uppdatera order</span>
                    </div>
                @else
                    <no-results title="Inga anpassade produkter/paket hittades"></no-results>
                @endif
            </div>
        </div>
        @if(!$school->deleted_at)
            <div class="align-right" style="width: 50%;">
                <button type="submit" class="btn btn-success save-school-prices-btn">Spara</button>
            </div>
        @endif
    </form>

    <form id="add-custom-addon-form" method="POST"
          action="{{ route($prefix . '::schools.create.customAddon', ['id' => $school->id]) }}"
          class="mt-3" enctype="multipart/form-data">
        <h4>Skapa anpassade produkter/paket</h4>
        {{ csrf_field() }}
        <input type="hidden" name="school-id" value="{{ $school->id }}">
        <div class="row">
            <div class="col-sm-7 col-lg-12 col-xl-6 col-xxl-7">
                <input type="text" name="name" maxlength="55" placeholder="Ny produkt/paket namn"
                       class="form-control form-control-inline">
            </div>
            <div class="col-sm-5 offset-lg-4 col-lg-8 offset-xl-0 col-xl-6 col-xxl-5">
                <input type="submit" class="btn btn-primary btn-sm" value="Skapa ny produkt/paket">
            </div>
        </div>
    </form>
</div>

<div class="tab-pane" id="tab-courses" role="tabpanel">
    @if(count($coursesVehicleSegments))
        <div class="row p-1">
            <select class="current-school-vehicle-filter">
                <option value="{{ null }}" selected>Välj Kurs</option>
                @foreach($coursesVehicleSegments as $coursesVehicleSegment)
                    <option @if(request()->has('vehicle_segment_id') && request()->vehicle_segment_id == $coursesVehicleSegment->id)
                                selected
                            @endif
                        value="{{ $coursesVehicleSegment->id }}">{{ $coursesVehicleSegment->label }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="clearfix">
        @if(auth()->user()->isAdmin())
            <a class="btn btn-sm btn-primary float-xs-right"
               href="{{ route('admin::courses.create', ['school' => $school->id]) }}">Lägg upp kurs</a>
        @else
            <a class="btn btn-sm btn-primary float-xs-right"
               href="{{ route('organization::courses.create', ['school' => $school->id]) }}">Lägg upp kurs</a>
        @endif
        <h3>Kommande kurser</h3>
    </div>
        @if(count($upcomingCourses))
            <div class="table">
                <div class="table-head table-row">
                    <div class="table-cell col-md-3">
                        Kursnamn
                    </div>
                    <div class="table-cell col-md-3">
                        Datum/tid
                    </div>
                    <div class="table-cell col-md-3 text-center">
                        Deltagare
                    </div>
                    <div class="table-cell col-md-3 text-center">
                        Tillgängliga platser
                    </div>
                </div>
                @foreach($upcomingCourses as $course)
                    <div class="table-row">
                        <div class="table-cell col-md-3">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin::courses.show', ['id' => $course->id]) }}">{{ $course->name }}</a>
                                ({{ $course->segment->vehicle->label }})
                            @else
                                <a href="{{ route('organization::courses.show', ['id' => $course->id]) }}">{{ $course->name }}</a>
                                ({{ $course->segment->vehicle->label }})
                            @endif
                        </div>
                        <div class="table-cell col-md-3">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin::courses.show', ['id' => $course->id]) }}">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</a>
                            @else
                                <a href="{{ route('organization::courses.show', ['id' => $course->id]) }}">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</a>
                            @endif
                        </div>
                        <div class="table-cell col-md-3 text-center">
                            {{ $course->bookings->where('cancelled', false)->count() }}<span class="hidden-md-up"> deltagare</span>
                        </div>
                        <div class="table-cell col-md-3 text-center">
                            {{ $course->seats }} <span class="hidden-md-up"> platser kvar</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <h4>No Courses</h4>
        @endif
        <h3>Avklarade kurser</h3>
        @if(count($completedCourses))
            <div class="table">
                <div class="table-head table-row">
                    <div class="table-cell col-md-3">
                        Kursnamn
                    </div>
                    <div class="table-cell col-md-3">
                        Datum/tid
                    </div>
                    <div class="table-cell col-md-3 text-center">
                        Deltagare
                    </div>
                    <div class="table-cell col-md-3 text-center">
                        Tillgängliga platser
                    </div>
                </div>
                @foreach($completedCourses as $course)
                    <div class="table-row">
                        <div class="table-cell col-md-3">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin::courses.show', ['id' => $course->id]) }}">{{ $course->name }}</a>
                                ({{ $course->segment->vehicle->label }})
                            @else
                                <a href="{{ route('organization::courses.show', ['id' => $course->id]) }}">{{ $course->name }}</a>
                                ({{ $course->segment->vehicle->label }})
                            @endif
                        </div>
                        <div class="table-cell col-md-3">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin::courses.show', ['id' => $course->id]) }}">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</a>
                            @else
                                <a href="{{ route('organization::courses.show', ['id' => $course->id]) }}">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</a>
                            @endif
                        </div>
                        <div class="table-cell col-md-3 text-center">
                            {{ $course->bookings->where('cancelled', false)->count() }}<span class="hidden-md-up"> deltagare</span>
                        </div>
                        <div class="table-cell col-md-3 text-center">
                            {{ $course->seats }} <span class="hidden-md-up"> platser kvar</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <h4>No Courses</h4>
        @endif

</div>

<div class="tab-pane" id="tab-gallery" role="tabpanel">
    <div class="gallery-list">
        @foreach ($school->images as $image)
            <div class="gallery-item">
                <a class="gallery-close" href="{{ auth()->user()->isAdmin()
                    ? route('admin::schools.images.delete', $image->id)
                    : route('organization::schools.images.delete', $image->id) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 25 25">
                        <path style="fill: #fff" d="M21 6l-3 18h-12l-3-18h2.028l2.666 16h8.611l2.666-16h2.029zm-4.711-4c-.9 0-1.631-1.099-1.631-2h-5.316c0 .901-.73 2-1.631 2h-5.711v2h20v-2h-5.711z"/>
                    </svg>
                </a>

                <img src="{{ str_replace('storage/upload//storage/upload', 'storage/upload', $image->thumbnailUrl) }}" alt="{{$image->alt_text}}" class="img-thumbnail"
                     style="max-width: 300px">
            </div>
        @endforeach
    </div>

    {!! Form::open([
        'route' => [
            auth()->user()->isAdmin() ? 'admin::schools.images.store' : 'organization::schools.images.store',
            $school->id
        ],
        'method' => 'post',
        'files' => true
    ]) !!}
        <div class="col-lg-6 col-xl-4 px-0">
            <div class="form-group">
                <label for="">Bild*</label>
                {!! Form::file('image', ['class' => 'form-control', 'required' => 'required']) !!}
            </div>
            <div class="form-group">
                <label for="">Namn*</label>
                {!! Form::text('file_name', '',['class' => 'form-control', 'required' => 'required']) !!}
            </div>
            <div class="form-group">
                <label for="">Beskrivning*</label>
                {!! Form::text('alt_text', '',['class' => 'form-control', 'required' => 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
            </div>
        </div>
    {!! Form::close() !!}

</div>
@if(auth()->user()->isAdmin())
    <div class="tab-pane" id="tab-fees" role="tabpanel">
        <form method="POST" action="{{ route('admin::schools.update.fees', ['id' => $school->id]) }}#fees"
              enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="row">
                <div class="col-lg-6">
                    <h3>Priser</h3>
                    @if($prices->count())
                        <div id="accordion" role="tablist" aria-multiselectable="true">

                            @foreach($prices->where('segment.editable', true)->groupBy('segment.vehicle_id') as $vehicleGroup)
                                @php($vehicleGroup = $vehicleGroup->sortBy('sort_order')->values())
                                <div class="mb-1">
                                    <div class="card-header collapsed" role="tab"
                                         id="heading-{{ $vehicleGroup->first()->segment->vehicle_id }}-fee"
                                         data-toggle="collapse" data-parent="#accordion"
                                         href="#collapse-{{ $vehicleGroup->first()->segment->vehicle_id }}-fee"
                                         aria-expanded="true"
                                         aria-controls="collapse-{{ $vehicleGroup->first()->segment->vehicle_id }}-fee"
                                    >
                                        <span class="float-xs-right">@icon('angle-up', 'lg')</span>
                                        <h5 class="mb-0">
                                            {{ trans('vehicles.' . strtolower($vehicleGroup->first()->segment->vehicle->name)) }}
                                            ({{ trans('vehicles.short.' . strtolower($vehicleGroup->first()->segment->vehicle->name)) }}
                                            )
                                        </h5>
                                    </div>

                                    <div id="collapse-{{ $vehicleGroup->first()->segment->vehicle_id }}-fee"
                                         class="collapse @if($errors->has('prices.*')) in @endif" role="tabpanel"
                                         aria-labelledby="heading-{{ $vehicleGroup->first()->segment->vehicle_id }}-fee">
                                        <div class="card-block">
                                            <div class="list"
                                                 data-vehicle="{{ $vehicleGroup->first()->segment->vehicle_id }}">

                                                @foreach($vehicleGroup as $price)
                                                    <div class="list-item" id="segment-{{ $price->segment->id }}-fee">
                                                        <h4>{{ trans('vehicle_segments.' . strtolower($price->segment->name)) }}
                                                            , {{ trans('vehicles.' . strtolower($price->segment->vehicle->name)) }}</h4>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div
                                                                    class="form-group @if($errors->has('fees.' . $price->id . '.fee')) has-error @endif mb-0">
                                                                    <label class="form-control-label"
                                                                           for="fees[{{ $price->id }}][fee]">Fee</label>
                                                                    <div class="input-group input-group-sm">
                                                                        <input hidden name="fees[{{$price->id}}][id]"
                                                                               value="{{$price->id}}">
                                                                        @php(
                                                                            $fee = (float)$price->fee
                                                                                ?: (array_key_exists($price->vehicle_segment_id, $pricesDefaults)
                                                                                ? (float)$pricesDefaults[$price->vehicle_segment_id]
                                                                                : config('fees.courses'))
                                                                        )
                                                                        @php($loyaltyDiscount = $loyaltyLevels[$schoolLoyaltyData['loyalty_level']]['discount'])
                                                                        <input class="form-control" type="number"
                                                                               name="fees[{{ $price->id }}][fee]"
                                                                               step=".1"
                                                                               id="fees[{{ $price->id }}][fee]"
                                                                               value="{{ $fee }}">
                                                                        <span class="input-group-addon">
                                                                        @if(in_array($price->segment->id, \Jakten\Models\VehicleSegment::LOYALTY_DISCOUNT_LIST))
                                                                                {{$fee}} - <span
                                                                                    title="Loyalty Discount">{{$loyaltyDiscount}}</span>
                                                                                + <span
                                                                                    title="Top Partner Fee">{{$topPartnerFee}}</span>
                                                                                = {{$fee - $loyaltyDiscount + $topPartnerFee}}
                                                                                %
                                                                            @else
                                                                                %
                                                                            @endif
                                                                    </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-lg-6">
                    <h3>Tilläggsprodukter -
                        <small>Lägg till produkter och sälj dem tillsammans med dina kurser via Körkortsjakten</small>
                    </h3>

                    <span data-vehicle="addons">
                    @foreach($addons as $addon)
                            <div id="segment-{{ $addon['id'] }}-fee"
                                 class="form-group @if($errors->has('addons.' . $addon['id'] . '.id') || $errors->has('addons.' . $addon['id']. '.price')) has-error @endif mb-0">
                            <label class="custom-control custom-checkbox" style="display: block;">
                                <span
                                    class="custom-control-description text"><strong>{{ $addon['name'] }}</strong></span>
                            </label>
                            <div class="input-toggle-content" id="input-addon-id-{{ $addon['id'] }}">
                                <div>
                                    <label class="form-control-label">Fee:</label>
                                    <input hidden name="addons[{{$addon['id']}}][id]" value="{{$addon['id']}}">
                                    <input class="form-control form-control-sm" type="number"
                                           name="addons[{{$addon['id']}}][fee]" min="0" step=".1"
                                           value="{{ $school->addons->where('id', $addon['id'])->first() && $school->addons->where('id', $addon['id'])->first()->pivot->fee ? (float)$school->addons->where('id', $addon['id'])->first()->pivot->fee : (int)config('fees.packages') }}"
                                    >
                                </div>
                            </div>
                        </div>
                        @endforeach
                </span>
                    <hr>
                    <h3>Anpassade produkter/paket</h3>
                    @if(count($customAddons))

                        @php($customAddons = $customAddons->sortBy('sort_order')->values())
                        <span data-vehicle="customAddons">
                        @foreach($customAddons as $customAddon)
                                <div class="form-group mb-0" id="segment-{{ $customAddon->id }}">
                                <label class="custom-control custom-checkbox" style="display: block;">
                                    <span
                                        class="custom-control-description text"><strong>{{ $customAddon->name }}</strong></span>
                                </label>
                                <div class="input-toggle-content" id="input-customAddons-id-{{ $customAddon->id }}">
                                    <div>

                                        <input hidden name="customAddons[{{$customAddon['id']}}][id]"
                                               value="{{$customAddon->id}}">
                                        <label class="form-control-label">Fee:</label>
                                        <input class="form-control form-control-sm" type="number" step=".1"
                                               name="customAddons[{{$customAddon->id}}][fee]" min="0"
                                               value="{{ $customAddon->fee ?: (int)config('fees.packages') }}">
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            @endforeach
                    </span>
                    @else
                        <no-results title="Inga anpassade produkter/paket hittades"></no-results>
                    @endif
                </div>
            </div>
            @if(!$school->deleted_at)
                <div class="align-right">
                    <button type="submit" class="btn btn-success save-school-prices-btn">Spara</button>
                </div>
            @endif
        </form>
    </div>
@endif
