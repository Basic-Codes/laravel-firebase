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
              <h5 class="text-center">Parked</h5>
            </div>
          </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Serving</h5>
            </div>
          </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Queue</h5>
                <ul class="list-group">
                    <li class="list-group-item bg-dark text-light">
                        <span class="d-flex justify-content-between">
                            <strong class="h5 mb-0">00001</strong>
                            <span>
                                <button class="btn btn-sm btn-primary"><i class="fas fa-phone-square"></i></button>
                                <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i></button>
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
          </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Called</h5>
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
        curr_user_id = JSON.parse("{{ json_encode($curr_user->id) }}")
        // ⚽️ Second Way
        // curr_user_id = {!! json_encode($curr_user->id) !!};

        addToQ = (type) => {
            phone = document.querySelector('input[name="phone"]').value
            
            data = {
                'curr_user_id': curr_user_id,
                'phone': phone,
                'type': type
            }

            $.ajax({
                url: '{{route('q.add')}}',
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
    </script>
    
@endsection