<form action="{{ route('paid-to-delivery-boy') }}" method="POST">
    @csrf
    <input type="hidden" name="delivery_boy_id" value="{{ $delivery_boy_info->user_id }}">
    <div class="modal-header">
    	<h5 class="modal-title h6">{{translate('Pay To Delivery Boy')}}</h5>
    	<button type="button" class="close" data-dismiss="modal">
    	</button>
    </div>
    <div class="modal-body">
      <table class="table table-striped table-bordered" >
          <tbody>
            <tr>
                    <td>{{ translate('Delivery Boy Name') }}</td>
                    <td>{{ translate('Earning') }}</td>
            </tr>
            <tr>
                <td>{{ $delivery_boy_info->user->name }}</td>
                    <td>{{ single_price($delivery_boy_info->total_earning) }}</td>
            </tr>
          </tbody>
      </table>

          <div class="form-group row">
              <label class="col-md-3 col-from-label" for="paid_amount">{{translate('Amount')}}</label>
              <div class="col-md-9">
                  <input type="number" lang="en" min="0" step="0.01" name="paid_amount" id="paid_amount" value="{{ $delivery_boy_info->total_earning }}" class="form-control" required>
              </div>
          </div>

    </div>
    <div class="modal-footer">
          <button type="submit" class="btn btn-primary">{{translate('Pay')}}</button>
      <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
    </div>
</form>

<script>
  $(document).ready(function(){
  });
</script>
