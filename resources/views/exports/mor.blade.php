<style>
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }

    .text-center {
        text-align: center;
    }
</style>
<table>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <td colspan="88">PT. SURYA BUANA LESTARIJAYA</td>        
    </tr>

    <tr>
        <td>MONTH</td>
        <td>:JUNE 2023</td>
        <td></td>
        <td></td>
        <td>LOCATION</td>
        <td>: FEDERAL II</td>
        <td colspan="82"></td>
    </tr>

    <tr>
        <td rowspan="3"> NO </td>
        <td rowspan="3"> DESCRIPTION </td>
        <td rowspan="3"> BRAND </td>
        <td rowspan="3"> SIZE </td>
        <td rowspan="3"> UNIT </td>
        <td rowspan="3"> PRICE </td>
        <td rowspan="2" colspan="2">OPENING STOCK</td>
        <td colspan="10">RECEIVING FOOD MATERIALS</td>
        <td rowspan="3" colspan="2">TOTAL</td>
        <td rowspan="2" colspan="62">MATERIAL INVENTORY CONTROL SHEET</td>
        <td colspan="2">TOTAL ISSUE</td>
        <td colspan="2">END STOCK</td>
        <td colspan="2">PHYSIC INVENTORY </td>
    </tr>

    <tr>
        <td colspan="2"> WEEK-1 </td>
        <td colspan="2"> WEEK-2 </td>
        <td colspan="2"> WEEK-3 </td>
        <td colspan="2"> WEEK-4 </td>
        <td colspan="2"> WEEK-V </td>
    </tr>

    <tr>
        <td colspan="2">31.04.2023</td>
        <td colspan="2">7/1/23</td>
        <td colspan="2">7/7/23</td>
        <td colspan="2">7/7/23</td>
        <td colspan="2">7/7/23</td>
        <td colspan="2">7/7/23</td>
        <td colspan="62">DAILY</td>
        
        <td>QTY</td>
        <td>QMOUNT</td>
        <td>QTY</td>
        <td>QMOUNT</td>
        <td> QTY </td>
        <td>QMOUNT</td>
    </tr>

    <tr>
        <td> CATEGORY </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td></td>
        <td></td>
        <td>1</td>
        <td></td>
        <td>2</td>
        <td></td>
        <td>3</td>
        <td></td>
        <td>4</td>
        <td></td>
        <td>5</td>
        <td></td>
        <td>6</td>
        <td></td>
        <td>7</td>
        <td></td>
        <td>8</td>
        <td></td>
        <td>9</td>
        <td></td>
        <td>10</td>
        <td></td>
        <td>11</td>
        <td></td>
        <td>12</td>
        <td></td>
        <td>13</td>
        <td></td>
        <td>14</td>
        <td></td>
        <td>15</td>
        <td></td>
        <td>16</td>
        <td></td>
        <td>17</td>
        <td></td>
        <td>18</td>
        <td></td>
        <td>19</td>
        <td></td>
        <td>20</td>
        <td></td>
        <td>21</td>
        <td></td>
        <td>22</td>
        <td></td>
        <td>23</td>
        <td></td>
        <td>24</td>
        <td></td>
        <td>25</td>
        <td></td>
        <td>26</td>
        <td></td>
        <td>27</td>
        <td></td>
        <td>28</td>
        <td></td>
        <td>29</td>
        <td></td>
        <td>30</td>
        <td></td>
        <td>31</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @foreach ($item_product as $category_code => $sub_category)
        @php
            $no = 0;
        @endphp
        @foreach ($sub_category as $sub_category_code => $product)
            @php
                $no++;
                $first_item = $product->first();
            @endphp
            <tr>
                <td rowspan="">
                    @if ($no == 1)
                        {{ $first_item->item_category->category }}
                    @endif
                </td>
                <td colspan="5"> {{ $first_item->sub_item_category->category }}</td>
                <td colspan="82"></td>
            </tr>

            <tr>
                <td></td>
                <td> Beef Blade </td>
                <td> Aust/Nz </td>
                <td> 1000 gr </td>
                <td> Kg </td>
                <td> 117,500 </td>
                <td> 2 </td>
                <td> 235,000 </td>
                <td> 10 </td>
                <td> 1,175,000 </td>
                <td> 15 </td>
                <td> 1,762,500 </td>
                <td> 10 </td>
                <td> 1,175,000 </td>
                <td> 10 </td>
                <td> 1,175,000 </td>
                <td> 10 </td>
                <td> 1,175,000 </td>
                <td> 57 </td>
                <td> 6,697,500 </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td>5</td>
                <td> 587,500 </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td>5</td>
                <td> 587,500 </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td>5</td>
                <td> 587,500 </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td>6</td>
                <td> 705,000 </td>
                <td></td>
                <td> - </td>
                <td>4</td>
                <td> 470,000 </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td>3</td>
                <td> 352,500 </td>
                <td>4</td>
                <td> 470,000 </td>
                <td></td>
                <td> - </td>
                <td>4</td>
                <td> 470,000 </td>
                <td></td>
                <td> - </td>
                <td></td>
                <td> - </td>
                <td>5</td>
                <td> 587,500 </td>
                <td> 41 </td>
                <td> 4,817,500 </td>
                <td> 16 </td>
                <td> 1,880,000 </td>
                <td> 15.0 </td>
                <td> 1,762,500 </td>
            </tr>
        @endforeach

        <tr>
            <td> B </td>
            <td> TOTAL FROZEN META </td>
            <td></td>
            <td></td>
            <td></td>
            <td> - </td>
            <td></td>
            <td> 3,729,475 </td>
            <td></td>
            <td> 9,590,560 </td>
            <td></td>
            <td> 10,797,360 </td>
            <td></td>
            <td> 10,415,960 </td>
            <td></td>
            <td> 10,103,065 </td>
            <td></td>
            <td> 9,138,960 </td>
            <td></td>
            <td> 53,775,380 </td>
            <td></td>
            <td> 1,500,000 </td>
            <td></td>
            <td> 961,635 </td>
            <td></td>
            <td> 1,406,000 </td>
            <td></td>
            <td> 1,712,860 </td>
            <td></td>
            <td> 1,593,735 </td>
            <td></td>
            <td> 1,346,240 </td>
            <td></td>
            <td> 1,063,015 </td>
            <td></td>
            <td> 1,436,500 </td>
            <td></td>
            <td> 1,760,205 </td>
            <td></td>
            <td> 1,289,700 </td>
            <td></td>
            <td> 1,225,815 </td>
            <td></td>
            <td> 1,730,035 </td>
            <td></td>
            <td> 1,774,990 </td>
            <td></td>
            <td> 1,765,000 </td>
            <td></td>
            <td> 1,543,500 </td>
            <td></td>
            <td> 2,017,020 </td>
            <td></td>
            <td> 1,253,500 </td>
            <td></td>
            <td> 1,106,500 </td>
            <td></td>
            <td> 1,115,225 </td>
            <td></td>
            <td> 1,056,035 </td>
            <td></td>
            <td> 1,066,835 </td>
            <td></td>
            <td> 1,058,500 </td>
            <td></td>
            <td> 1,628,500 </td>
            <td></td>
            <td> 1,271,315 </td>
            <td></td>
            <td> 1,461,950 </td>
            <td></td>
            <td> 1,669,200 </td>
            <td></td>
            <td> 2,247,205 </td>
            <td></td>
            <td> 1,932,000 </td>
            <td></td>
            <td> 998,700 </td>
            <td></td>
            <td> 2,181,205 </td>
            <td></td>
            <td> 1,632,265 </td>
            <td></td>
            <td> 45,805,185 </td>
            <td></td>
            <td> 7,970,195 </td>
            <td></td>
            <td> 8,136,170 </td>
        </tr>
    @endforeach

    <tr></tr>

    <tr>
        <td></td>
        <td> GRAND TOTAL </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> - </td>
        <td> 20,099,325 </td>
        <td></td>
        <td> 39,995,798 </td>
        <td></td>
        <td> 40,211,158 </td>
        <td></td>
        <td> 39,186,923 </td>
        <td></td>
        <td> 38,407,232 </td>
        <td></td>
        <td> 37,488,376 </td>
        <td></td>
        <td> 174,922,710 </td>
        <td></td>
        <td> 6,250,682 </td>
        <td></td>
        <td> 6,732,615 </td>
        <td></td>
        <td> 5,915,797 </td>
        <td></td>
        <td> 5,939,603 </td>
        <td></td>
        <td> 5,874,359 </td>
        <td></td>
        <td> 5,127,460 </td>
        <td></td>
        <td> 5,485,088 </td>
        <td></td>
        <td> 7,599,972 </td>
        <td></td>
        <td> 6,062,535 </td>
        <td></td>
        <td> 5,299,956 </td>
        <td></td>
        <td> 5,285,293 </td>
        <td></td>
        <td> 5,752,332 </td>
        <td></td>
        <td> 5,744,725 </td>
        <td></td>
        <td> 6,018,462 </td>
        <td></td>
        <td> 8,464,748 </td>
        <td></td>
        <td> 5,880,498 </td>
        <td></td>
        <td> 6,472,572 </td>
        <td></td>
        <td> 5,814,175 </td>
        <td></td>
        <td> 5,137,694 </td>
        <td></td>
        <td> 4,634,445 </td>
        <td></td>
        <td> 4,950,804 </td>
        <td></td>
        <td> 6,984,491 </td>
        <td></td>
        <td> 6,126,799 </td>
        <td></td>
        <td> 5,969,644 </td>
        <td></td>
        <td> 5,934,597 </td>
        <td></td>
        <td> 6,013,908 </td>
        <td></td>
        <td> 6,336,834 </td>
        <td></td>
        <td> 6,503,209 </td>
        <td></td>
        <td> 5,820,421 </td>
        <td></td>
        <td> 8,397,509 </td>
        <td></td>
        <td> 5,376,132 </td>
        <td></td>
        <td> 193,510,645 </td>
        <td></td>
        <td> 39,842,717 </td>
        <td></td>
        <td> 38,949,282 </td>
    </tr>

    <tr></tr>
    <tr></tr>
    <tr></tr>

    <tr>
        <td></td>
        <td>CATEGORY</td>
        <td>WEEK</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>TOTAL</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>OPENING STOCK</td>
        <td>I</td>
        <td>II</td>
        <td>III</td>
        <td>VI</td>
        <td>V</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>A</td>
        <td>FROZEN FOODS</td>
        <td> 3,729,475 </td>
        <td> 9,590,560 </td>
        <td> 10,797,360 </td>
        <td> 10,415,960 </td>
        <td> 10,103,065 </td>
        <td> 9,138,960 </td>
        <td> 53,775,380 </td>
        <td></td>
        <td></td>
        <td>   </td>
        <td> </td>
        <td>Prepared by:</td>
        <td></td>
        <td></td>
        <td>Prepared by:</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>B</td>
        <td>DAIRY PRODUCT</td>
        <td> 1,086,853 </td>
        <td> 3,503,252 </td>
        <td> 3,830,086 </td>
        <td> 3,568,496 </td>
        <td> 3,121,181 </td>
        <td> 4,042,540 </td>
        <td> 19,152,408 </td>
        <td></td>
        <td></td>
        <td>   </td>
        <td></td>
        <td> </td>
        <td></td>
        <td></td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>


    <tr>
        <td></td>
        <td>TOTAL</td>
        <td> 20,099,325 </td>
        <td> 39,995,798 </td>
        <td> 40,211,158 </td>
        <td> 39,186,923 </td>
        <td> 38,407,232 </td>
        <td> 37,488,376 </td>
        <td> 215,388,812 </td>
        <td></td>
        <td></td>
        <td>   </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>FHISICAL INVENTORY</td>
        <td> - </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> 38,949,282 </td>
        <td> 38,949,282 </td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>CONSUMPTION</td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> 176,439,531 </td>
        <td> 176,439,531 </td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>MANDAYS</td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> 1,364 </td>
        <td> 1,369 </td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>FOOD COST</td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> 129,354 </td>
        <td> 128,882 </td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>