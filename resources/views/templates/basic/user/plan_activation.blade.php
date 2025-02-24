@extends($activeTemplate.'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="mb-4">
                <h3 class="mb-2">@lang('Plan Activation')</h3>
            </div>
            <div class="card custom--card">
                <div class="card-body">

                    <?php
                    if (isset(auth()->user()->plan_id) && auth()->user()->total_invest >= 100) {
                        $style ="display:none";
                        echo '<p style="color:red"><i>You can upgrate plan only one time!!</i></p>';
                    }

                    ?>


                    <form action="{{ route('user.plan.activationTopUpSave') }}" method="post" style="{{@$style}}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">@lang('Activation/Topup')</label>
                            <!-- <input type="number" class="form-control form--control" name="number" required autocomplete="off"> -->
                            <select name="plan_type" class="form-control form--control">
                                <option value="activate">Activation</option>
                                <?php
                                if (isset(auth()->user()->plan_id) && auth()->user()->plan_id > 0) {
                                ?>
                                    <option value="upgrade">Up-Gradation</option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                        if (isset(auth()->user()->plan_id) && auth()->user()->plan_id > 0) {
                        ?>
                            <!-- <div class="form-group">
                                <label class="form-label">@lang('User ID')</label>
                                <input type="text" class="form-control form--control user_id" name="user_id" required autocomplete="off">
                                <strong id="username"></strong>
                            </div> -->

                        <?php
                        }
                        ?>
                        <div class="form-group">
                            <label class="form-label">@lang('Amount')</label>
                            <div class="input-group">
                                <!-- <input class="form-control form--control" name="user_plan_amount" type="number" value="{{ old('user_plan_amount') }}" step="any" autocomplete="off" required>
                                <span class="input-group-text">{{ $general->cur_text }}</span> -->


                            <select name="user_plan_amount" class="form-control form--control">
                                 
                                   <?php
                                    for ($i = 50; $i <= 500; $i += 50) {
                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                    ?>
                                 
                                ?>
                            </select><span class="input-group-text">{{ $general->cur_text }}</span>


                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($) {

        $('.user_id').on('keyup paste', function(e) {

            if ($(this).val().length < 7) {
                return;
            }
            $.ajax({
                type: "post",
                url: "{{route('user.get_user_name_by_user_id')}}",
                data: {

                    user_id: $(this).val(),
                    _token: "{{csrf_token()}}"
                },
                success: function(data) {
                    console.log(data.data);
                    if (data.status == 'success') {
                        $('#username').html(data.data);

                    } else {
                        $('#username').html('');
                    }
                }
            });
        });

    })(jQuery);
</script>
@endpush