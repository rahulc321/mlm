@extends($activeTemplate . 'layouts.master')
@section('content')

<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">@lang('My Tree')</h3>
    </div>

    <div class="mb-4">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="text-center">@lang('Referrer Link')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form>
                            <div class="form-group">
                                {{-- <label class="form-label">@lang('Join')</label> --}}
                                <div class="copy-link">
                                    <input class="copyURL w-100" type="text" value="{{ route('user.register') }}/?ref={{ auth()->user()->username }}" readonly>
                                    <span class="copyBoard" id="copyBoard">
                                        <i class="las la-copy"></i>
                                        <strong class="copyText">@lang('Copy')</strong>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- <div class="col-md-6">
                        <form>
                            <div class="form-group">
                                <label class="form-label">@lang('Join right')</label>
                                <div class="copy-link">
                                    <input class="copyURL2 w-100" type="text" value="{{ route('home') }}/?ref={{ auth()->user()->username }}&position=right" readonly>
                                    <span class="copyBoard2" id="copyBoard2">
                                        <i class="las la-copy"></i>
                                        <strong class="copyText2">@lang('Copy')</strong>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="card custom--card mt-4 mb-4 tree">
        <div class="card-body">
            @php
            $loopNumber = 1;
            $totalLoop = 0;
            @endphp
            @for ($i = 1; $i <= 4; $i++) <div class="row justify-content-center llll text-center">
                @for ($in = 1; $in <= $loopNumber; $in++) <div class="w-{{ $loopNumber }}">
                    @php echo $mlm->showSingleUserinTree($tree[$mlm->getHands()[$totalLoop]]); @endphp
        </div>
        @php
        $totalLoop++;
        @endphp
        @endfor
    </div>
    @php
    $loopNumber = $loopNumber * 2;
    @endphp
    @endfor
</div>
</div>
</div>

<div class="modal fade user-details-modal-area" id="exampleModalCenter" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">@lang('User Details')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="@lang('Close')">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="user-details-modal">
                    <div class="user-details-header">
                        <div class="thumb"><img class="tree_image w-h-100-p" src="#" alt="*"></div>
                        <div class="content">
                        <span class="user-status tree_username"></span>
                            <a class="user-name tree_url tree_name" href=""></a>
                            <span class="user-status tree_status"></span>
                            <span class="user-status tree_plan"></span>
                        </div>
                       
                       
                    </div>
                    
                    <div class="user-details-body text-center">

                        <div class="copy-link">
                            <input class="copyURL3 w-100" type="text" value="{{ route('user.register') }}/?ref={{ auth()->user()->username }}" readonly>
                            <span class="copyBoard3" id="copyBoard3">
                                <i class="las la-copy"></i>
                                <strong class="copyText3">@lang('Copy')</strong>
                            </span>
                        </div>

                        <h6 class="my-3">@lang('Referred By'): <span class="tree_ref"></span></h6>

                        <table class="table--responsive--md table">
                            <thead>
                                <th>&nbsp;</th>
                                <th>@lang('LEFT')</th>
                                <th>@lang('RIGHT')</th>
                            </thead>

                            <tr>
                                <td>@lang('Current BV')</td>
                                <td><span class="lbv"></span></td>
                                <td><span class="rbv"></span></td>
                            </tr>
                            <tr>
                                <td>@lang('Free Member')</td>
                                <td><span class="lfree"></span></td>
                                <td><span class="rfree"></span></td>
                            </tr>

                            <tr>
                                <td>@lang('Paid Member')</td>
                                <td><span class="lpaid"></span></td>
                                <td><span class="rpaid"></span></td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('style')
<link href="{{ asset($activeTemplateTrue . 'users/css/tree.css') }}" rel="stylesheet">
<style>
    .copied::after {
        background-color: #{{ $general->base_color }
    }

    ;
    }


/* Smartphones (portrait and landscape) ----------- */
@media only screen and (min-device-width : 320px) and (max-device-width : 480px) {
/* Styles */
.card.custom--card.tree{
            position: relative;


margin: 0;
                position: relative;
    overflow-y: scroll;
    width: 100%;
    height: auto;
    display: block;
    }
    .card.custom--card.tree .card-body{
                /* position: absolute; */
    width: 500%;  
      /* overflow-y: scroll; */
    }
        .user .user-name {
        display: block;
    }
}

/* Smartphones (landscape) ----------- */
@media only screen and (min-width : 321px) {
/* Styles */
}

/* Smartphones (portrait) ----------- */
@media only screen and (max-width : 320px) {
/* Styles */
}

@media (min-width:320px)  { 
    
    /* smartphones, iPhone, portrait 480x320 phones */ }
@media (min-width:481px)  { /* portrait e-readers (Nook/Kindle), smaller tablets @ 600 or @ 640 wide. */ }
@media (min-width:641px)  { /* portrait tablets, portrait iPad, landscape e-readers, landscape 800x480 or 854x480 phones */ }
@media (min-width:961px)  { /* tablet, landscape iPad, lo-res laptops ands desktops */ }
@media (min-width:1025px) { /* big landscape tablets, laptops, and desktops */ }
@media (min-width:1281px) { /* hi-res laptops and desktops */ }

</style>
@endpush
@push('script')
<script>
    "use strict";
    (function($) {
        $('.viewteammember').on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            window.location.href=$(this).attr('href');
        });
        $('.showDetails').on('click', function() {

            $('.copyURL3').val($('.copyURL').val()+'&place='+$(this).find('.viewteammember').text());
            
            var modal = $('#exampleModalCenter');

            $('.tree_name').text($(this).data('name'));
            $('.tree_url').attr({
                "href": $(this).data('treeurl')
            });
            $('.tree_status').text($(this).data('status'));
            $('.tree_username').text($(this).data('username'));
            $('.tree_plan').text($(this).data('plan'));
            $('.tree_image').attr({
                "src": $(this).data('image')
            });
            $('.user-details-header').removeClass('Paid');
            $('.user-details-header').removeClass('Free');
            $('.user-details-header').addClass($(this).data('status'));
            $('.tree_ref').text($(this).data('refby'));
            $('.lbv').text($(this).data('lbv'));
            $('.rbv').text($(this).data('rbv'));
            $('.lpaid').text($(this).data('lpaid'));
            $('.rpaid').text($(this).data('rpaid'));
            $('.lfree').text($(this).data('lfree'));
            $('.rfree').text($(this).data('rfree'));
            $('#exampleModalCenter').modal('show');
        });

        $('#copyBoard').click(function() {
            var copyText = document.getElementsByClassName("copyURL");
            copyText = copyText[0];
            copyText.select();
            copyText.setSelectionRange(0, 99999);

            /*For mobile devices*/
            document.execCommand("copy");
            $('.copyText').text('Copied');
            setTimeout(() => {
                $('.copyText').text('Copy');
            }, 2000);
        });
        $('#copyBoard2').click(function() {
            var copyText = document.getElementsByClassName("copyURL2");
            copyText = copyText[0];
            copyText.select();
            copyText.setSelectionRange(0, 99999);

            /*For mobile devices*/
            document.execCommand("copy");
            $('.copyText2').text('Copied');
            setTimeout(() => {
                $('.copyText2').text('Copy');
            }, 2000);
        });
        $('#copyBoard3').click(function() {
            var copyText = document.getElementsByClassName("copyURL3");
            copyText = copyText[0];
            copyText.select();
            copyText.setSelectionRange(0, 99999);

            /*For mobile devices*/
            document.execCommand("copy");
            $('.copyText3').text('Copied');
            setTimeout(() => {
                $('.copyText3').text('Copy');
            }, 2000);
        });


    })(jQuery);
</script>
@endpush