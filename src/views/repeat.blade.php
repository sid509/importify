<div class="row" style="display: flex"  >
    <input class="form-control" type="text" name="name[]" class="form-control" style="width: 200px;" readonly value="{{ $col }}" />&nbsp;
    <select class="form-control" name="value[]" style="width: 400px;">
        <option value="*">Select this row from excel</option>
        @foreach($rowInFile as $col)
        <option value="{{$col}}">{{$col}}</option>
        @endforeach
    </select>&nbsp;
</div>
