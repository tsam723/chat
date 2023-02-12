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

                    </ul>
                    <form action="" method="post">
                    {{ csrf_field() }}
                    <input type="text" id="text" name="text" placeholder="Send a message">
                    <input type="submit" value="Send">
                    </form>
                    <?php 
                            echo Auth::user()->name;

                            
                        ?>
                    <script>
                        console.log("test");
                        $("form").on("submit", function (e) {
                            $.ajax({
                            type: "POST",
                            url: "/submit",
                            data: $(this).serialize(),
                            success: function () {
                                // Display message back to the user here 
                                $("input[type='text']").val("");
                                console.log("uspesno!");
                            }
                            });
                            e.preventDefault();
                        });
                  
                        const evtSource = new EventSource("/stream");
                        
                        evtSource.addEventListener('open', function(e) {
                        // Connection was opened.
                            console.log("Opening new connection");
                        }, false);

                        evtSource.onmessage = (event) => {
                        
                        const eventList = document.getElementById("list");


                        const trenutenId = JSON.parse(event.data).id;
                        console.log(trenutenId);
                        
                        var id = "{{ Auth::user()->id }}";
                        //console.log(id);

                            if(trenutenId == id){
                                //izpiši levo
                                const newElement = document.createElement("li");
                                newElement.id="left";
                                newElement.textContent = JSON.parse(event.data).username + ": " + JSON.parse(event.data).msg;
                                eventList.appendChild(newElement);
                            }else{
                                //izpiši desno
                                const newElementRight = document.createElement("li");
                                newElementRight.id="demo";
                                newElementRight.textContent = JSON.parse(event.data).username + ": " + JSON.parse(event.data).msg;
                                eventList.appendChild(newElementRight);
                            }
                            
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
