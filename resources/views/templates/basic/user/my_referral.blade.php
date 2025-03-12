@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner bg-light py-4">
    <div class="mb-4 text-center">
        <h3 class="mb-2 text-dark">@lang('My Referrals')</h3>
    </div>
    
    {{-- Tabs Navigation --}}
    <ul class="nav nav-tabs mb-3 justify-content-center" id="referralTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="level1-tab" data-bs-toggle="tab" href="#level1" role="tab">@lang('Level 1')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="level2-tab" data-bs-toggle="tab" href="#level2" role="tab">@lang('Level 2')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="level3-tab" data-bs-toggle="tab" href="#level3" role="tab">@lang('Level 3')</a>
        </li>
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content" id="referralTabsContent">
        
        {{-- Level 1 --}}
        <div class="tab-pane fade show active" id="level1" role="tabpanel">
            <div class="row">
                @foreach($firstStageUsers as $user)
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card custom--card gradient-card text-center p-4 shadow">
                        <div class="card-body">
                            <h5 class="mb-2 text-white fw-bold">{{ @$user->firstname.' '.@$user->lastname }}</h5>
                            <p class="mb-0 text-white"><strong>@lang('Ref Code'):</strong> {{ @$user->username }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Level 2 --}}
        <div class="tab-pane fade" id="level2" role="tabpanel">
            <div class="row">
               @foreach($secondStageUsers as $user)
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card custom--card gradient-card text-center p-4 shadow">
                        <div class="card-body">
                            <h5 class="mb-2 text-white fw-bold">{{ @$user->firstname.' '.@$user->lastname }}</h5>
                            <p class="mb-0 text-white"><strong>@lang('Ref Code'):</strong> {{ @$user->username }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Level 3 --}}
        <div class="tab-pane fade" id="level3" role="tabpanel">
            <div class="row">
                @foreach($thirdStageUsers as $user)
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card custom--card gradient-card text-center p-4 shadow">
                        <div class="card-body">
                            <h5 class="mb-2 text-white fw-bold">{{ @$user->firstname.' '.@$user->lastname }}</h5>
                            <p class="mb-0 text-white"><strong>@lang('Ref Code'):</strong> {{ @$user->username }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
<style>
    /* Page Background */
.dashboard-inner {
    background: linear-gradient(120deg, #f3f4f6, #e2e8f0);
    min-height: 100vh;
    padding: 20px;
}

/* Gradient Cards */
.gradient-card {
    background: linear-gradient(135deg, #6a11cb, #2575fc);
    border: none;
    border-radius: 12px;
    color: white;
    transition: transform 0.3s ease-in-out;
}

.gradient-card:hover {
    transform: translateY(-5px);
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
}

/* Nav Tabs */
.nav-tabs .nav-link {
    color: #333;
    font-weight: 600;
    padding: 10px 15px;
    border-radius: 8px;
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #6a11cb, #2575fc);
    color: white;
}

</style>
@endsection
