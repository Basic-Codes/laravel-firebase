@extends('master')

@section('content')
    
<div class="row mb-5">
    <div class="col">
        <div class="card">
            <div class="card-body d-flex justify-content-end">
                <div style="margin-left: 10px;" class="dropdown">
                    <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                      {{$curr_user->name ?? ''}}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        @foreach ($users as $user_i)
                            <li><a class="dropdown-item" href="{{route('home', ['user_id'=>$user_i->id])}}">{{$user_i->name}}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div style="margin-left: 10px;" class="dropdown">
                    <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                      {{$curr_shop->name ?? ''}}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        @foreach ($shops as $shop_i)
                            <li><a class="dropdown-item" href="{{route('shop.active', ['shop_id'=>$shop_i->id])}}">{{$shop_i->name}}</a></li>
                        @endforeach
                    </ul>
                </div>
                <button @if(!$curr_user) disabled @endif style="margin-left: 10px;" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addShopModal">
                    Add Shop
                </button>
                <button style="margin-left: 10px;" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Add User
                </button>
                <div class="d-flex">
                    <input style="margin-left: 10px;"class="form-control" name="phone" type="number" placeholder="Phone Number" aria-label="Search">
                    <button onclick="addToQ('queue')" style="margin-left: 5px;"  class="btn btn-primary" type="submit"><i class="fas fa-plus-circle"></i></button>
                    <button onclick="addToQ('serve')" style="margin-left: 5px;"  class="btn btn-success" type="submit"><i class="fas fa-arrow-circle-right"></i></button>
                    <button onclick="addToQ('park')" style="margin-left: 5px;"  class="btn btn-warning" type="submit"><i class="fas fa-archive"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-3">
        <div class="card">
            <div class="card-body">
              <h5 class="text-center mb-3">Parked</h5>
              <ul class="list-group" id="park_list">
                {{-- <li class="list-group-item bg-dark text-light mb-2">
                    <span class="d-flex justify-content-between align-items-center">
                        <strong class="h5 mb-0">00001</strong>
                        <span>
                            <button class="btn btn-sm btn-primary"><i class="fas fa-play"></i></button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-bell"></i></button>
                        </span>
                    </span>
                </li> --}}
            </ul>
            </div>
          </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center mb-3">Serving</h5>
                <ul class="list-group" id="serve_list">
                    {{-- <li class="list-group-item bg-dark text-light mb-2">
                        <span class="d-flex justify-content-between align-items-center">
                            <strong class="h5 mb-0">00001</strong>
                            <span>
                                <button class="btn btn-sm btn-primary"><i class="fas fa-check-double"></i></button>
                                <button class="btn btn-sm btn-light"><i class="fas fa-archive"></i></button>
                            </span>
                        </span>
                    </li> --}}
                </ul>
            </div>
          </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center mb-3">Queue</h5>
                <ul class="list-group" id="queue_list">
                    {{-- <li class="list-group-item bg-dark text-light mb-2">
                        <span class="d-flex justify-content-between align-items-center">
                            <strong class="h5 mb-0">00001</strong>
                            <span>
                                <button class="btn btn-sm btn-primary"><i class="fas fa-phone-alt"></i></button>
                                <button class="btn btn-sm btn-info"><i class="fas fa-concierge-bell"></i></button>
                                <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-warning"><i class="fas fa-archive"></i></button>
                            </span>
                        </span>
                    </li> --}}
                </ul>
            </div>
          </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center mb-3">Called</h5>
                <div class="d-flex justify-content-start">
                    <button onclick="call()" class="btn btn-lg btn-primary"><i class="fas fa-phone-alt"></i></button>
                    <button onclick="complete()" class="btn btn-lg btn-success mx-2"><i class="fas fa-concierge-bell"></i></button>
                </div>
                <div class="text-center my-3 py-3 rounded-3" style="background: rgb(209, 209, 209)">
                    <h1 class="display-2" style="font-weight: 800;" id="called_item">0</h1>
                </div>
            </div>
          </div>
    </div>
</div>


@endsection


@section('scripts')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <script>
        // ⚽️ First Way
        // curr_user_id = JSON.parse("{{ json_encode($curr_user->id ?? 'X') }}")
        // ⚽️ Second Way
        curr_user_id = {!! json_encode(($curr_user->id ?? 'X')) !!};
        curr_shop_id = {!! json_encode(($curr_shop->id ?? 'X')) !!};

        // =========================================================
        //         Utility function for sending ajax request
        // =========================================================
        sendAjax = (url, data) => {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
                success: function (result) {
                    console.log(result);
                },
                error: function (error) {
                    console.log(error);
                },
            });
        }


        // =========================
        //         Add Queue
        // =========================
        addToQ = (type) => {
            phone_input = document.querySelector('input[name="phone"]')
            phone = phone_input.value

            data = {
                'curr_user_id': curr_user_id,
                'phone': phone,
                'type': type
            }

            sendAjax('{{route('q.add')}}', data)
            phone_input.value = ''
        }


        call = (id = 'X') => {
            data = {
                'curr_user_id': curr_user_id,
                'id': id
            }

            sendAjax('{{route('q.call')}}', data)
        }
        serve = (id = 'X') => {
            data = {
                'curr_user_id': curr_user_id,
                'id': id
            }
            
            sendAjax('{{route('q.serve')}}', data)
        }
        park = (id = 'X') => {
            data = {
                'curr_user_id': curr_user_id,
                'id': id
            }
            
            sendAjax('{{route('q.park')}}', data)
        }
        resume = (id = 'X') => {
            data = {
                'curr_user_id': curr_user_id,
                'id': id
            }
            
            sendAjax('{{route('q.serve')}}', data)
        }
        complete = (id = 'X') => {
            data = {
                'curr_user_id': curr_user_id,
                'id': id
            }
            
            sendAjax('{{route('q.complete')}}', data)
        }
    </script>


    {{-- ----------------------------------------------------------------- --}}
    {{--                          Firebase Rendering                       --}}
    {{-- ----------------------------------------------------------------- --}}
    <script>
        // =========================
        //           Queue
        // =========================
        var renderQ = (snapshot) => {
            queue_list = document.querySelector('#queue_list')
            queue_list.innerHTML = ''
            snapshot.forEach((child) => {
                // console.log(child.key, child.val());

                new_el = document.createElement('div')
                new_el.innerHTML = `<li class="list-group-item bg-dark text-light mb-2">
                                        <span class="d-flex justify-content-between align-items-center">
                                            <strong class="h5 mb-0">${child.val().position}</strong>
                                            <span>
                                                <button onclick="call(${child.val().id})" class="btn btn-sm btn-primary"><i class="fas fa-phone-alt"></i></button>
                                                <button onclick="serve(${child.val().id})" class="btn btn-sm btn-info"><i class="fas fa-concierge-bell"></i></button>
                                                <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i></button>
                                                <button onclick="park(${child.val().id})" class="btn btn-sm btn-warning"><i class="fas fa-archive"></i></button>
                                            </span>
                                        </span>
                                    </li>`;
                queue_list.appendChild(new_el) 
            });
        };
        // =========================
        //           Serve
        // =========================
        var renderServe = (snapshot) => {
            serve_list = document.querySelector('#serve_list')
            serve_list.innerHTML = ''
            snapshot.forEach((child) => {
                // console.log(child.key, child.val());

                new_el = document.createElement('div')
                new_el.innerHTML = `<li class="list-group-item bg-dark text-light mb-2">
                                        <span class="d-flex justify-content-between align-items-center">
                                            <strong class="h5 mb-0">${child.val().position}</strong>
                                            <span>
                                                <button onclick="complete(${child.val().id})" class="btn btn-sm btn-primary"><i class="fas fa-check-double"></i></button>
                                                <button onclick="park(${child.val().id})" class="btn btn-sm btn-light"><i class="fas fa-archive"></i></button>
                                            </span>
                                        </span>
                                    </li>`;
                serve_list.appendChild(new_el) 
            });
        };
        // =========================
        //            Park
        // =========================
        var renderPark = (snapshot) => {
            park_list = document.querySelector('#park_list')
            park_list.innerHTML = ''
            snapshot.forEach((child) => {
                // console.log(child.key, child.val());

                new_el = document.createElement('div')
                new_el.innerHTML = `<li class="list-group-item bg-dark text-light mb-2">
                                        <span class="d-flex justify-content-between align-items-center">
                                            <strong class="h5 mb-0">${child.val().position}</strong>
                                            <span>
                                                <button onclick="resume(${child.val().id})" class="btn btn-sm btn-primary"><i class="fas fa-play"></i></button>
                                                <button class="btn btn-sm btn-light"><i class="fas fa-bell"></i></button>
                                            </span>
                                        </span>
                                    </li>`;
                park_list.appendChild(new_el) 
            });
        };
        // =========================
        //           Called
        // =========================
        var renderCalled = (snapshot) => {
            called_item = document.querySelector('#called_item')
            console.log(snapshot);
            snapshot.forEach((child) => {
                // console.log(child.key, child.val());

                called_item.innerText = child.val().position  || 0
            });
            if(!snapshot.val()) called_item.innerText = 0
        };
    </script>
    
    {{-- ----------------------------------------------------------------- --}}
    {{--                          Firebase Setup                           --}}
    {{-- ----------------------------------------------------------------- --}}
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.0.1/firebase-app.js";
        import { getDatabase, ref, onValue, set } from "https://www.gstatic.com/firebasejs/9.0.1/firebase-database.js";
        
        const firebaseConfig = {
            apiKey: "AIzaSyBw6Gz1Iip73-RiTWgE1H76I7FsP9Yhe7c",
            authDomain: "pushnotification-xxxxxxxx.firebaseapp.com",
            databaseURL: "https://pushnotification-xxxxxxxx-default-rtdb.firebaseio.com",
            projectId: "pushnotification-xxxxxxxx",
            storageBucket: "pushnotification-xxxxxxxx.appspot.com",
            messagingSenderId: "836414008971",
            appId: "1:836414008971:web:f4b8faf34e0fbb35852e8e"
        };

        const app = initializeApp(firebaseConfig);

        console.log(`${curr_user_id}/${curr_shop_id}/queue`);

        const db = getDatabase();


        const queueRef = ref(db, `${curr_user_id}/${curr_shop_id}/queue`);
        onValue(queueRef, (snapshot) => {
            const data = snapshot.val();
            // console.log(data);
            renderQ(snapshot)
        });
        const serveRef = ref(db, `${curr_user_id}/${curr_shop_id}/serve`);
        onValue(serveRef, (snapshot) => {
            const data = snapshot.val();
            // console.log(data);
            renderServe(snapshot)
        });
        const parkRef = ref(db, `${curr_user_id}/${curr_shop_id}/park`);
        onValue(parkRef, (snapshot) => {
            const data = snapshot.val();
            // console.log(data);
            renderPark(snapshot)
        });
        const calledRef = ref(db, `${curr_user_id}/${curr_shop_id}/called`);
        onValue(calledRef, (snapshot) => {
            const data = snapshot.val();
            // console.log(data);
            renderCalled(snapshot)
        });

        
        // function writeUserData() {
        //     set(ref(db, 'user/'), {
        //         name: 'Ghost',
        //         email: 'ghost@gmail.com',
        //     });
        // }
        // writeUserData()
    </script>
    
@endsection