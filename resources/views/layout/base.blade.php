<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<style>
    * {
        margin: 0;
    }

    body {
        margin-right: 20px;
        margin-left: 17px;
        margin-top: 20px;
        padding: 0;
    }

    .title{
        padding-bottom: 20px;
    }
    .quarter_row {
        border: .8px solid black;
        width: 100%;

    }
    .item_row {
        border-bottom: .8px solid black;
        border-right: .8px solid black;
    }
    .activity {
        border: .8px solid black;
    }
    .amount_brought_forward {
        border-left: .8px solid black;
        border-right: .8px solid black;
    }
    .activity_total {
        border-bottom: .8px solid black;
        border-right: .8px solid black;
    }
    .column1 {
        float: left;
        width: 7%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 4px;
        min-height: 25px;
    }
    .column2 {
        float: left;
        width: 50%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 4px;
        text-align: left;
        min-height: 25px;

    }
    .column3 {
        float: left;
        width: 12.5%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 4px;
        min-height: 25px;
    }
    .column4 {
        float: left;
        width: 12.5%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 4px;
        min-height: 25px;
    }
    .column5 {
        float: left;
        width: 18%;
        border-left: .8px solid black;
        padding: 4px;
        min-height: 25px;
    }

    .pre_column1 {
         float: left;
         width: 30%;
         border-left: .8px solid black;
         border-right: .8px solid black;
         padding: 4px;
         min-height: 25px;
        font-size: small;
     }

    .pre_column2 {
        float: left;
        width: 30%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 4px;
        min-height: 25px;
        font-size: small;
    }

    .pre_column3 {
        float: left;
        width: 20%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 4px;
        min-height: 25px;
        font-size: small;
    }

    .pre_column4 {
        float: left;
        width: 20%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 4px;
        min-height: 25px;
        font-size: small;
    }

    .total {
        float: left;
        width: 50%;
        padding: 4px;
        min-height: 25px;
    }

    .income_totals{
        float: left;
        width: 16.6%;
        padding: 4px;
        min-height: 25px;
    }

    .row::after {
        content: "";
        display: table;
        clear: both;
    }
    .summary_num {
        float: left;
        width: 5%;
        padding: 4px;
        min-height: 25px;
        font-weight: bolder;
        text-transform: uppercase;
        border-right: .8px solid black;
        font-size: small;
    }
    .summary {
        float: left;
        width: 45%;
        padding: 4px;
        min-height: 25px;
        font-weight: bolder;
        text-transform: uppercase;
        border-right: .8px solid black;
        font-size: small;
    }
    .activity_summary_num {
        float: left;
        width: 10%;
        padding: 10px;
        min-height: 15px;
        font-weight: bolder;
        text-transform: capitalize;
        border-right: .8px solid black;
        font-size: small;
    }
    .activity_summary_num_contribution {
        float: left;
        width: 2%;
        padding: 10px;
        min-height: 15px;
        font-weight: bolder;
        text-transform: capitalize;
        border-right: .8px solid black;
        font-size: small;
    }
    .activity_summary_label {
        float: left;
        width: 30%;
        padding: 10px;
        min-height: 15px;
        font-weight: bolder;
        text-transform: capitalize;
        border-right: .8px solid black;
        font-size: small;
    }
    .activity_summary {
        float: left;
        width: 45%;
        padding: 10px;
        min-height: 15px;
        font-weight: bolder;
        text-transform: capitalize;
        border-right: .8px solid black;
        font-size: small;
    }
    .activity_summary_end {
        float: left;
        width: 45%;
        padding: 10px;
        min-height: 15px;
        font-weight: bolder;
        text-transform: capitalize;
        font-size: medium;
    }
    .totals {
        float: left;
        width: 50%;
        padding: 4px;
        font-weight: bolder;
        min-height: 25px;
        text-transform: capitalize;
        font-size: medium;
    }

    .fin_sec {
        min-width: 27%;
    }
    .treasurer {
        min-width: 27%;
    }
    .border_line {
        width: 30px;
        min-width: 30%;
        margin-left: 270px;
    }

    .page-break {
        page-break-inside: auto;
        page-break-after: always;

    }
</style>
<body>
    @yield('section')
</body>

</html>
