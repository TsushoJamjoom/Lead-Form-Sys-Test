<p>
    Company Name: <strong>{{ $data->company->company_name }} {!! $salesLeadCountByCompany > 0 ? '<b style="color:yellow;">&nbsp; &#10026;</b>' : '' !!}</strong>
    <br />
    Created By: <strong>{{ $data->createdBy->name }}</strong>
    <br />
    @if (!empty($visitTime))
        Visit Time: <strong>{{ str_replace('|', '', $visitTime) }}</strong>
        <br />
    @endif
    @if (!empty($data->customer_satisfaction))
        Satisfaction Scale : {{ $data->customer_satisfaction }}
        <br />
    @endif
    @if(!empty($data->createdBy->profile_photo))
        <img src="{{asset('storage/user-profile/' . $data->createdBy->profile_photo)}}" width="70px" height="70px" alt="Avatar" class="bg-info rounded-circle"><br>
    @endif
    @if ($appointStatus != 'closed')
        @permission('customer/view')
        <a href="{{ $routeReadMore }}" class="text-decoration-none read-more-info">Read More</a>
        @endpermission
        @permission('calendar/delete')
            <a type="button" class="btn btn-danger btn-sm mt-2 event-delete" id="delete-{{ $data->id }}">Delete</a>
        @endpermission
        @permission('calendar/edit')
        <a type="button" class="btn btn-warning btn-sm mt-2 event-edit" id="edit-{{ $data->id }}">Edit</a>
        @endpermission
    @endif
    @if($showMissedNote)
        <a type="button" class="text-decoration-none read-more-info event-add-note cursor-pointer" id="note-{{ $data->id }}">Add Reason</a>
    @elseif(@$data->note)
        Reason: <strong>{{ $data->note }}</strong>
    @endif
</p>
