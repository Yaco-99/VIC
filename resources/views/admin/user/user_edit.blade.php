@extends('layouts.baseadmin')
@section('content')
    <h1>Editer un Utilisateur</h1>
    <div class="formContainerAdmin ">
        <form method="POST" action="/admin/user/edit/{{$user->id}}">
            @csrf
            {{--  Prénom  --}}
            <user-input-text required="true" id="first_name" type="text" label="Prénom" value="{{ $user->first_name }}" error="@error('first_name') is-invalid @enderror">
                @error('first_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </user-input-text>
            {{--  Nom  --}}
            <user-input-text required="true" id="last_name" type="text" label="Nom" value="{{ $user->last_name }}" error="@error('last_name') is-invalid @enderror">
                @error('last_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </user-input-text>
            {{--  Email  --}}
            <user-input-text required="true" id="email" type="email" label="Email" value="{{ $user->email }}" error="@error('email') is-invalid @enderror">
                @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </user-input-text>
            {{--  role  --}}
            <user-input-dropdown value="{{ $user->role }}" required="true" id="role" label="Grade" error="@error('role') is-invalid @enderror">
                <option value="0" @if($user->role == 0)selected @endif>Utilisateur</option> 
                <option value="1" @if($user->role == 1)selected @endif>Coach</option>
                <option value="2" @if($user->role == 2)selected @endif>Parrain</option>
                <option value="3" @if($user->role == 3)selected @endif>Admin</option>
                @error('role')
                    <span class="invalid-feedback" role="alert" v-slot:error>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </user-input-dropdown>
            {{--  adresse  --}}
            <user-input-text required="false" id="location" type="text" label="Adresse" value="{{ $user->location }}" error="@error('location') is-invalid @enderror">
                @error('location')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </user-input-text>
            {{--  birth date  --}}
            <user-input-text required="false" id="birth_date" type="date" label="Date de naissance" value="{{ $user->birth_date }}" error="@error('birth_date') is-invalid @enderror">
                @error('birth_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </user-input-text>
            {{--  cpas status  --}}
            <user-input-text required="false" id="cpas_status" type="text" label="Status CPAS" value="{{ $user->cpas_status }}" error="@error('cpas_status') is-invalid @enderror">
                @error('cpas_status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </user-input-text>
            {{--  description  --}}
            <user-input-text required="false" id="description" type="text" label="Description" value="{{ $user->description }}" error="@error('description') is-invalid @enderror">
                @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </user-input-text>
            {{--  coach  --}}
            <user-input-dropdown value="{{ $user->coach_id }}" required="false" id="coach_id" label="Coach" error="@error('coach_id') is-invalid @enderror">
                <option value='' @if($user->coach_id == null)selected @endif>Aucun</option>
                @foreach ($coachs as $coach)
                    <option value={{$coach->id}} @if($user->coach_id == $coach->id)selected @endif>{{$coach->first_name}}</option>
                @endforeach
                @error('coach_id')
                    <span class="invalid-feedback" role="alert" v-slot:error>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </user-input-dropdown>
            {{--  sponsor  --}}
            <user-input-dropdown value="{{ $user->sponsor_id }}" required="false" id="sponsor_id" label="Parrain" error="@error('sponsor_id') is-invalid @enderror">
                <option value='' @if($user->sponsor_id == null)selected @endif>Aucun</option>
                @foreach ($sponsors as $sponsor)
                    <option value={{$sponsor->id}} @if($user->sponsor_id == $sponsor->id)selected @endif>{{$sponsor->first_name}}</option>
                @endforeach
                @error('sponsor_id')
                    <span class="invalid-feedback" role="alert" v-slot:error>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </user-input-dropdown>
            <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-2">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Enregistrer les modifications') }}
                    </button>
                    @if($updated == true)
                        <span class="text-success">Les modifications ont bien été enregistrée !</span>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection