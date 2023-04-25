{{ csrf_field() }}

<div class="form-group">
	<label for="price-id">Pristyp</label>
	<select id="price-id" name="price_id">
	    @foreach($priceTypes as $priceType)
	        <option value="{{ $priceType->id }}" @if(old('price_id', $price->price_id) == $priceType->id) selected @endif >{{ $priceType->label }}, {{ $priceType->vehicle->label }}</option>
	    @endforeach
	</select>
</div>

<div class="form-group @if($errors->has('amount')) has-error @endif">
	<label for="amount">Pris</label>
	<input type="number" id="amount" name="amount" value="{{ old('amount', $price->amount) }}" />
</div>

<div class="form-group @if($errors->has('quantity')) has-error @endif">
	<label for="quantity">Antal (minuter/tilfällen/annat)</label>
	<input type="number" id="quantity" name="quantity" value="{{ old('quantity', $price->quantity) }}" />
</div>

<div class="form-group @if($errors->has('comment')) has-error @endif">
	<label for="comment">Kommentar</label>
	<textarea id="comment" name="comment">{{ old('comment', $price->comment) }}</textarea>
</div>

<div class="form-group">
	<input type="radio" id="is_subject_to_change" name="subject_to_change" value="1" @if(old('subject_to_change', $price->subject_to_change)) checked @endif>
	<label for="is_subject_to_change">Priset kan ändras</label>
</div>

<div class="form-group">
	<input type="radio" id="not_subject_to_change" name="subject_to_change" value="0" @if(!old('subject_to_change', $price->subject_to_change)) checked @endif>
	<label for="not_subject_to_change">Priset kan inte ändras</label>
</div>