<html>
<center>
    <tr>
        <td colspan="3"> </td>
        <td>
        <h1>
          @if($cat == 0)
            ERP Navigation
          @elseif($cat == 1)
           Portal Navigation
          @elseif($cat == 2)
           Operation Navigation
          @elseif($cat == 3)
           Hrms Navigation
          @elseif($cat == 4)
           Manufacturing Navigation
           @elseif($cat == 5)
           Document Restriction Policy
           @endif
        </h1>  </td>
        <td colspan="3"> </td>

    <tr>
</center>
<table>
   @if($cat != 5 )
    <thead>
         <tr style="background-color: #6798da;">
                    <th >Module Name</th>
                    <th >Navigation</th>
                    <th >Document</th>
                    <th >Action</th>
         </tr>
      
    </thead>
    <tbody>
          @foreach ($mainMenus as $item)
          <tr style="">
                <td >{{$item->description}}<br></td>
                @isset($item->children)
                @foreach ($item->children as $nav)
                <tr>
                <td colspan="1"> </td>
                <td >
                    <div>
                      {{$nav->description}} <br />
                    </div>
                </td>

                @if(isset($nav->children))
                @foreach ($nav->children as $nav3)
                <tr>
                <td colspan="2"> </td>
                <td>
                        <div>
                        <span> {{ $nav3->description }}  <br /></span> <br />
                        </div>   

                       
                       
                        <td>

                            @if($nav3->readonly == true)
                            <span>
                            Readonly,
                            </span>
                            @else
                            <span>
                             N/A,
                            </span>
                            @endif

                            @if($nav3->create == true)
                            <span>
                            Create,
                            </span>
                            @else
                            <span>
                             N/A,
                            </span>
                            @endif


                            @if($nav3->update == true)
                            <span>
                                Edit,
                            </span>
                            @else
                            <span>
                             N/A,
                            </span>
                            @endif


                            @if($nav3->delete == true)
                            <span>
                                Delete,
                            </span>
                            @else
                            <span>
                             N/A,
                            </span>
                            @endif

                            @if($nav3->print == true)
                            <span>
                            Print,
                            </span>
                            @else
                            <span>
                             N/A,
                            </span>
                            @endif

                            @if($nav3->export == true)
                            <span>
                            Export to CSV
                            </span>
                            @else
                            <span>
                             N/A
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
                        <span> Not Available<br /></span> <br />
                        </div>   
                        
                         <td>

                            @if($nav->readonly == true)
                           <span>
                              Readonly,
                           </span>
                           @else
                            <span>
                             N/A,
                            </span>
                            @endif

                           @if($nav->create == true)
                           <span>
                              Create,
                           </span>
                           @else
                            <span>
                             N/A,
                            </span>
                            @endif


                           @if($nav->update == true)
                           <span>
                               Edit,
                           </span>
                           @else
                            <span>
                             N/A,
                            </span>
                            @endif


                           @if($nav->delete == true)
                           <span>
                                Delete,
                           </span>
                           @else
                            <span>
                             N/A,
                            </span>
                            @endif

                           @if($nav->print == true)
                           <span>
                              Print,
                           </span>
                           @else
                            <span>
                             N/A,
                            </span>
                            @endif

                           @if($nav->export == true)
                           <span>
                           Export to CSV
                           </span>
                           @else
                            <span>
                             N/A
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
                        <span >{{nl2br(e($item->policyDescription)) }} <br /></span> <br />
                    </div>
                    @else 
                    <div>
                        <span >{{nl2br(e($item->policyDescription)) }} -<b> N/A</b> <br /></span> <br />
                    </div>
                    @endif
         
        </td>
    @endforeach
    @endif
    
</table>

</html>