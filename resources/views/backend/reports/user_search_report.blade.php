@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
    		<div class="card-header">
    			<h1 class="h6">{{translate('User Search Report')}}</h1>
    		
			<form action="{{ route('user_search_report.index') }}" method="GET">
                   
                        <div class="col-md-2 d-none">
                            <button class="btn btn-primary" id="btn_target" type="submit">{{ translate('Filter') }}</button>
                        </div>
						<input type="hidden" name="roport_id" id="roport_id" value="0">
						<div class="col-md-2">
                           <span data-href="" id="export" class="btn btn-primary" onclick="exportTasks(event.target);">Export</span>
                        </div>
                    
                </form>
				</div>
            <div class="card-body">
			
                <table class="table table-bordered aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Search By') }}</th>
                            <th>{{ translate('Number searches') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($searches as $key => $searche)
                            <tr>
                                <td>{{ ($key+1) + ($searches->currentPage() - 1)*$searches->perPage() }}</td>
                                <td>{{ $searche->query }}</td>
                                <td>{{ $searche->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination mt-4">
                    {{ $searches->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   function exportTasks(_this) {
	   document.getElementById("roport_id").value = "1";
	   document.getElementById("btn_target").click();
	   document.getElementById("roport_id").value = "0";
   }
</script>
@endsection
