@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body" id="chat">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                    <br>
                    <ul id="list">
                    <li id="demo"></li>
                    </ul>
                    <form action="submit" method="post">
                    {{ csrf_field() }}
                    <input type="text" class="text" name="text" placeholder="Send a message">
                    <input type="submit" value="Send">
                    </form>
                    <?php 
                            $id = Auth::id();
                            $name = DB::table('users')->where('id', $id)->value('name');
                            echo $name;
                            
                        ?>
                    <script>
                        console.log("test");
                        
                        /*
                        const evtSource = new EventSource("/stream");
                        
                        evtSource.addEventListener('msg1', function(e) {
                            const newElement = document.createElement("li");
                            const eventList = document.getElementById("list");
                            
                            newElement.textContent = event.data;
                            eventList.appendChild(newElement);
                        }, false);
                        */
                        
                        const evtSource = new EventSource("/stream");
                        
                        evtSource.addEventListener('open', function(e) {
                        // Connection was opened.
                            console.log("Opening new connection");
                        }, false);

                        evtSource.onmessage = (event) => {
                        const newElement = document.createElement("li");
                        const eventList = document.getElementById("list");
                        //const primer = document.getElementById("demo");

                        newElement.textContent = event.data;
                        eventList.appendChild(newElement);
                        console.log(event.data);
                        };
                        
                        evtSource.onerror = (err) => {
                        console.error("EventSource failed:", err);
                        };
                        
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
