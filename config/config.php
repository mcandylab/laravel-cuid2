<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CUID2 length
    |--------------------------------------------------------------------------
    |
    | The length of the generated identifier. The cuid2 standard is 24
    | characters. The range supported by visus/cuid2 is 4 to 32.
    |
    */

    'length' => (int) env('CUID2_LENGTH', 24),

];
