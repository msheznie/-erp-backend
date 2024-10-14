<html>
<table>
    <thead>
    <tr>
        @php
            $bigginingDt = new DateTime($entity['finance_year_by']['bigginingDate']);
            $bigginingDate = $bigginingDt->format('d/m/Y');

            $endingDt = new DateTime($entity['finance_year_by']['endingDate']);
            $endingDate = $endingDt->format('d/m/Y');


        @endphp
        <td>Finance Year : {{ $bigginingDate }} - {{ $endingDate }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>Year : {{ $entity['Year'] }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>Currency : {{ $currency['reportingcurrency']['CurrencyCode'] }}</td>
    </tr>
    <tr>

        <td>Segment : {{ $entity['segment_by']['ServiceLineDes'] }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>Template : {{ $entity['template_master']['description'] }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>Send Notification at {{ $entity['sentNotificationAt'] }}%</td>
    </tr>
    <tr></tr>
    <tr></tr>





    <tr>
        <th>#</th>
        <th>Category</th>
        @foreach ($months as $month)
            <th>{{$month['monthName']}} - {{$month['year']}}</th>
        @endforeach
        <th>Total	</th>

    </tr>
    </thead>
    @foreach($budgetDetails as $item)
        <tr>
                <?php $mainNo = $loop->index + 1 ?>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $item->description }}</td>



                <?php $type = $item->itemType ?>
                <?php $subCategoryTot = $item->subcategorytot ?>
                <?php $totNetAmounts = 0 ?>
                <?php $totYearNetAmounts = 0 ?>

                <?php $subTotId = array(); ?>



            @if($type == 3 && $subCategoryTot != "undefined")
                @foreach($subCategoryTot as $totSubCat)

                        <?php
                        $subId = $totSubCat->subcategory->detID;
                        array_push($subTotId,$subId);
                        ?>

                @endforeach
            @endif



            @if($type == 3)

                    <?php $totBudget = 0 ?>
                    <?php $totYearBudget = 0 ?>
                @foreach ($months as $month)
                        <?php $totBudget = 0 ?>

                        <?php $i = $loop->index ?>

                    @foreach($budgetDetails as $item)
                        @foreach($item->subcategory as $item1)

                            @foreach($item1->gl_codes->whereIn('templateDetailID', $subTotId) as $item2)

                                    <?php $totBudget += $item2->items[$i]->budjetAmtRpt ?>
                            @endforeach
                        @endforeach
                    @endforeach
                        <?php $totYearBudget += $totBudget ?>

                    <td>{{ number_format($totBudget,2) }}</td>
                @endforeach
                <td>{{ number_format($totYearBudget,2) }}</td>

            @endif


        @if($item->itemType == 1)
            @foreach($item->subcategory as $key => $item1)
                @php $isFinal = $item1->isFinalLevel @endphp
                @php $isItemType = $item1->itemType @endphp

                <tr>
                    <td>{{ $mainNo }}.{{ $loop->index + 1 }}</td>
                    <td>{{ $item1->description }}</td>


                    @if($isFinal == 0 && $isItemType == 2)
                       @foreach($item1->subcategory as $key2 => $subCategory)
                        <tr>
                            <td>{{ $mainNo }}.{{ $key + 1 }}.{{$key2+1}}</td>
                            <td>{{ $subCategory->description }}</td>
                            @foreach($months as $month)
                                <td>0</td>
                            @endforeach
                            <td>0</td>
                        </tr>

                        @if($subCategory->isFinalLevel == 0 && $subCategory->itemType == 2)
                            @foreach($subCategory->subcategory as $key3 => $subSubCategory)
                                <tr>
                                    <td>{{ $mainNo }}.{{ $key + 1 }}.{{$key2+1}}.{{$key3+1}}</td>
                                    <td>{{ $subSubCategory->description }}</td>
                                    @foreach($months as $month)
                                        <td>0</td>
                                    @endforeach
                                    <td>0</td>
                                </tr>

                                @if($subSubCategory->isFinalLevel == 0 && $subSubCategory->itemType == 2)
                                    @foreach($subSubCategory->subcategory as $key4 => $subSubSubCategory)
                                        <tr>
                                            <td>{{ $mainNo }}.{{ $key + 1 }}.{{$key2+1}}.{{$key3+1}}.{{$key4+1}}</td>
                                            <td>{{ $subSubSubCategory->description }}</td>
                                            @foreach($months as $month)
                                                <td>0</td>
                                            @endforeach
                                            <td>0</td>
                                        </tr>

                                        @if($subSubSubCategory->isFinalLevel == 0 && $subSubSubCategory->itemType == 2)
                                            @foreach($subSubSubCategory->subcategory as $key5 => $subSubSubSubCategory)
                                                <tr>
                                                    <td>{{ $mainNo }}.{{ $key + 1 }}.{{$key2 + 1}}.{{$key3 + 1}}.{{$key4 + 1}}.{{$key5 + 1}}</td>
                                                    <td>{{ $subSubSubSubCategory->description }}</td>
                                                    @foreach($months as $month)
                                                        <td>0</td>
                                                    @endforeach
                                                    <td>0</td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                       @endforeach
                    @else
                        <?php $subFinalTotId = array(); ?>

                        @foreach($item->subcategory as $totSubCat)
                            @php $isFinal = $totSubCat->isFinalLevel @endphp
                            @php $isType = $totSubCat->itemType @endphp
                            @if($isFinal == 1 && $isType == 3)

                                @foreach($totSubCat->gllink as $finalCat)
                                        <?php

                                        $subIdFinal = $finalCat->subCategory;
                                        array_push($subFinalTotId,$subIdFinal);
                                        ?>
                                @endforeach
                            @endif
                        @endforeach
                            <?php $totBudget = 0 ?>
                            <?php $totYear = 0 ?>
                            <?php $totYearBudget = 0 ?>

                        @if($isFinal == 1 && $isItemType == 3)
                                <?php $totBudget = 0 ?>
                            @foreach ($months as $month)
                                    <?php $totBudget = 0 ?>

                                    <?php $i = $loop->index ?>

                                @foreach($budgetDetails as $item)
                                    @foreach($item->subcategory as $item1)

                                        @foreach($item1->gl_codes->whereIn('templateDetailID', $subFinalTotId) as $item2)

                                                <?php $totBudget += $item2->items[$i]->budjetAmtRpt ?>
                                        @endforeach
                                    @endforeach
                                @endforeach
                                    <?php $totYearBudget += $totBudget ?>
                                <td>{{ number_format($totBudget,2) }}</td>
                            @endforeach
                            <td>{{ number_format($totYearBudget,2) }}</td>

                        @endif



                        @if($isFinal == 1 && $isItemType == 2)
                            @foreach ($months as $month)
                                    <?php $totBudget = 0 ?>
                                    <?php $i = $loop->index ?>
                                @foreach($item1->gl_codes as $item2)
                                        <?php $totBudget += $item2->items[$i]->budjetAmtRpt ?>
                                @endforeach
                                    <?php $totYear +=  $totBudget ?>
                                <td>{{ number_format($totBudget,2) }}</td>
                            @endforeach
                            <td>{{ number_format($totYear,2) }}</td>
                        @endif
                        @foreach($item1->gl_codes as $item2)
                            <tr>
                                <td></td>
                                <td>
                                    <strong></strong><strong>{{ $item2->glDescription }} | {{ $item2->glCode }}</strong>
                                </td>
                                    <?php $totBudgetYear = 0 ?>
                                @foreach($item2->items as $item3)
                                        <?php   $totBudgetYear += $item3->budjetAmtRpt ?>
                                    <td>{{ number_format($item3->budjetAmtRpt,2) }}</td>
                                @endforeach
                                <td>{{ number_format($totBudgetYear,2) }}</td>

                            </tr>
                            @endforeach
                    @endif

                    </tr>
                    @endforeach
                    @endif
                    </tr>

                    @endforeach
                    </tbody>
</table>
</html>
