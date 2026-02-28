<table class="no-border table table-slim mb-0">
    <tr>
        <td class="width-33 text-left">
            @if(!empty($customized['old_value']))
                <span class="label " style="background-color: #45c8f1 ;">{{$customized['old_value']}}</span>
            @endif
        </td>
        <td class="width-33 text-center">
            @if(!empty($customized['mid']))
                {{$customized['mid']}}
            @endif
        </td>
        <td class="width-33 text-right">
            @if(!empty($customized['new_value']))
                <span class="label " style="background-color: #4913b7 ;">{{$customized['new_value']}}</span>
            @endif
        </td>
    </tr>
</table>
