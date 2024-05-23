@if ($errors->any())
    <div id="errors" class="bg-danger white p2 w50 center mt2">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
