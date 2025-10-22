<html>
<center>
    <tr>
        <td colspan="3"> </td>
        <td>
        <h1>
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
        </h1>  </td>
        <td colspan="3"> </td>

    <tr>
</center>
<table>
   @if($cat != 5 )
    <thead>
         <tr style="background-color: #6798da;">
                    <th >{{ __('custom.module_name') }}</th>
                    <th >{{ __('custom.navigation') }}</th>
                    <th >{{ __('custom.document') }}</th>
                    <th >{{ __('custom.action') }}</th>
         </tr>
      
    </thead>
    <tbody>
          @foreach ($mainMenus as $item)
          <tr style="">
                <td >{{ $item->secondaryLanguageDescription ?: $item->description }}<br></td>
                @isset($item->children)
                @foreach ($item->children as $nav)
                <tr>
                <td colspan="1"> </td>
                <td >
                    <div>
                      {{ $nav->secondaryLanguageDescription ?: $nav->description }} <br />
                    </div>
                </td>

                @if(isset($nav->children))
                @foreach ($nav->children as $nav3)
                <tr>
                <td colspan="2"> </td>
                <td>
                        <div>
                        <span> {{ $nav3->secondaryLanguageDescription ?: $nav3->description }}  <br /></span> <br />
                        </div>   

                       
                       
                        <td>

                            @if($nav3->readonly == true)
                            <span>
                            {{ __('custom.readonly') }},
                            </span>
                            @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif

                            @if($nav3->create == true)
                            <span>
                            {{ __('custom.create') }},
                            </span>
                            @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif


                            @if($nav3->update == true)
                            <span>
                                {{ __('custom.edit') }},
                            </span>
                            @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif


                            @if($nav3->delete == true)
                            <span>
                                {{ __('custom.delete') }},
                            </span>
                            @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif

                            @if($nav3->print == true)
                            <span>
                            {{ __('custom.print') }},
                            </span>
                            @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif

                            @if($nav3->export == true)
                            <span>
                            {{ __('custom.export_to_csv') }}
                            </span>
                            @else
                            <span>
                             {{ __('custom.na') }}
                            </span>
                            @endif


                        </td>
                      
                  
                            
                </td>
                </tr>
                @endforeach
                @else 
                <tr>
                <td colspan="2"> </td>
                <td>
                         <div>
                        <span> {{ __('custom.not_available') }}<br /></span> <br />
                        </div>   
                        
                         <td>

                            @if($nav->readonly == true)
                           <span>
                              {{ __('custom.readonly') }},
                           </span>
                           @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif

                           @if($nav->create == true)
                           <span>
                              {{ __('custom.create') }},
                           </span>
                           @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif


                           @if($nav->update == true)
                           <span>
                               {{ __('custom.edit') }},
                           </span>
                           @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif


                           @if($nav->delete == true)
                           <span>
                                {{ __('custom.delete') }},
                           </span>
                           @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif

                           @if($nav->print == true)
                           <span>
                              {{ __('custom.print') }},
                           </span>
                           @else
                            <span>
                             {{ __('custom.na') }},
                            </span>
                            @endif

                           @if($nav->export == true)
                           <span>
                           {{ __('custom.export_to_csv') }}
                           </span>
                           @else
                            <span>
                             {{ __('custom.na') }}
                            </span>
                            @endif


                    </td>
                            
                </td>
                </tr>

                @endif

                </tr>
                
   

                @endforeach
                @endisset
         </tr>
          @endforeach
        
     
    </tbody>

    @elseif($cat == 5 )
    @foreach ($subMenus as $item)
    <tr>
        <td>
       
                    @if($item->isChecked == true)
                    <div>
                        <span >{{nl2br(e($item->policy_description_translated ?: $item->policyDescription)) }} <br /></span> <br />
                    </div>
                    @else 
                    <div>
                        <span >{{nl2br(e($item->policy_description_translated ?: $item->policyDescription)) }} -<b> {{ __('custom.na') }}</b> <br /></span> <br />
                    </div>
                    @endif
         
        </td>
    @endforeach
    @endif
    
</table>

</html>