<table>
    <tr>
        <td colspan="4" style="text-align: center; font-size: 14pt;">
            @if($cat == 0)
                {{ __('custom.erp_navigation') }}
            @elseif($cat == 1)
                {{ __('custom.portal_navigation') }}
            @elseif($cat == 2)
                {{ __('custom.operation_navigation') }}
            @elseif($cat == 3)
                {{ __('custom.hrms_navigation') }}
            @elseif($cat == 4)
                {{ __('custom.manufacturing_navigation') }}
            @elseif($cat == 5)
                {{ __('custom.document_restriction_policy') }}
            @endif
        </td>
    </tr>
    @if($cat != 5)
    <tr style="background-color: #6798da;">
        <td>{{ __('custom.module_name') }}</td>
        <td>{{ __('custom.navigation') }}</td>
        <td>{{ __('custom.document') }}</td>
        <td>{{ __('custom.action') }}</td>
    </tr>
    @foreach ($mainMenus ?? [] as $item)
    <tr>
        <td>{{ $item->secondaryLanguageDescription ?: $item->description }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @isset($item->children)
        @foreach ($item->children as $nav)
            @if(isset($nav->children) && count($nav->children) > 0)
                @foreach ($nav->children as $nav3)
                <tr>
                    <td></td>
                    <td></td>
                    <td>{{ $nav3->secondaryLanguageDescription ?: $nav3->description }}</td>
                    <td>
                        @if($nav3->readonly == true) {{ __('custom.readonly') }}, @else {{ __('custom.na') }}, @endif
                        @if($nav3->create == true) {{ __('custom.create') }}, @else {{ __('custom.na') }}, @endif
                        @if($nav3->update == true) {{ __('custom.edit') }}, @else {{ __('custom.na') }}, @endif
                        @if($nav3->delete == true) {{ __('custom.delete') }}, @else {{ __('custom.na') }}, @endif
                        @if($nav3->print == true) {{ __('custom.print') }}, @else {{ __('custom.na') }}, @endif
                        @if($nav3->export == true) {{ __('custom.export_to_csv') }} @else {{ __('custom.na') }} @endif
                    </td>
                </tr>
                @endforeach
            @else
            <tr>
                <td></td>
                <td>{{ $nav->secondaryLanguageDescription ?: $nav->description }}</td>
                <td>{{ __('custom.not_available') }}</td>
                <td>
                    @if($nav->readonly == true) {{ __('custom.readonly') }}, @else {{ __('custom.na') }}, @endif
                    @if($nav->create == true) {{ __('custom.create') }}, @else {{ __('custom.na') }}, @endif
                    @if($nav->update == true) {{ __('custom.edit') }}, @else {{ __('custom.na') }}, @endif
                    @if($nav->delete == true) {{ __('custom.delete') }}, @else {{ __('custom.na') }}, @endif
                    @if($nav->print == true) {{ __('custom.print') }}, @else {{ __('custom.na') }}, @endif
                    @if($nav->export == true) {{ __('custom.export_to_csv') }} @else {{ __('custom.na') }} @endif
                </td>
            </tr>
            @endif
        @endforeach
    @endisset
    @endforeach
    @elseif($cat == 5)
    @foreach ($subMenus ?? [] as $item)
    <tr>
        <td colspan="4">
            @if($item->isChecked == true)
                {{ $item->policy_description_translated ?: $item->policyDescription }}
            @else
                {{ $item->policy_description_translated ?: $item->policyDescription }} - {{ __('custom.na') }}
            @endif
        </td>
    </tr>
    @endforeach
    @endif
</table>
