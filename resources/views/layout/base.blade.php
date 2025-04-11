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
        margin-right: 15px;
        margin-left: 15px;
        margin-top: 20px;
        padding: 0;
    }

    .title{
        padding-bottom: 20px;
    }
    .row{
        border: 1px solid black;
        width: 100%;
    }
    .quarter_row {
        width: 100%;
    }

    .column1 {
        float: left;
        width: 7%;
        border-left: 1px solid black;
        padding: 4px;
        height: 18px;
    }
    .column2 {
        float: left;
        width: 50%;
        border-left: 1px solid black;
        padding: 4px;
        text-align: left;
        height: 18px;
    }
    .column3 {
        float: left;
        width: 12.5%;
        border-left: 1px solid black;
        padding: 4px;
        height: 18px;
    }
    .column4 {
        float: left;
        width: 12.5%;
        border-left: 1px solid black;
        padding: 4px;
        height: 18px;
    }
    .column5 {
        float: left;
        width: 18%;
        border-left: 1px solid black;
        padding: 4px;
        height: 18px;
        border-right: 2px solid black;
    }

    .pre_column1 {
         float: left;
         width: 30%;
         border-left: .8px solid black;
         border-right: .8px solid black;
         padding: 3px;
        font-size: small;
     }

    .pre_column2 {
        float: left;
        width: 30%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 3px;
        font-size: small;
    }

    .pre_column3 {
        float: left;
        width: 20%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 3px;
        min-height: 25px;
        font-size: small;
    }

    .pre_column4 {
        float: left;
        width: 20%;
        border-left: .8px solid black;
        border-right: .8px solid black;
        padding: 3px;
        min-height: 25px;
        font-size: small;
    }

    .total {
        float: left;
        width: 50%;
        padding: 3px;
        min-height: 25px;
    }

    .income_totals{
        float: left;
        width: 16.6%;
        padding: 3px;
        min-height: 25px;
    }

    .row::after {
        content: "";
        display: table;
        clear: both;
    }

    .detail{
        width: 100%;
    }
    .detail::after{
        content: "";
        display: table;
        clear: both;
    }
    .summary_num {
        float: left;
        width: 5%;
        padding: 3px;
        font-weight: bold;
        text-transform: uppercase;
        border-right: 1px solid black;
        font-size: small;
        height: 22px;
    }
    .summary {
        float: left;
        width: 45%;
        padding: 3px;
        font-weight: bold;
        text-transform: uppercase;
        border-right: 1px solid black;
        font-size: small;
        height: 22px;
    }
    .activity_summary_num {
        float: left;
        width: 10%;
        padding: 3px;
        font-weight: bold;
        text-transform: capitalize;
        border-right: 1px solid black;
        font-size: small;
        height: 18px;
    }
    .activity_summary_num_contribution {
        float: left;
        width: 2%;
        padding: 3px;
        font-weight: bold;
        text-transform: capitalize;
        border-right: 1px solid black;
        font-size: small;
        height: 18px;
    }
    .activity_summary_label {
        float: left;
        width: 30%;
        padding: 3px;
        font-weight: bold;
        text-transform: capitalize;
        border-right: 1px solid black;
        font-size: small;
        height: 18px;
    }
    .activity_summary {
        float: left;
        width: 45%;
        padding: 3px;
        font-weight: bold;
        text-transform: capitalize;
        border-right: 1px solid black;
        font-size: smaller;
        height: 18px;
    }
    .activity_summary_end {
        float: left;
        width: 45%;
        padding: 3px;
        font-weight: bold;
        text-transform: capitalize;
        font-size: smaller;
        height: 18px;
    }
    .totals {
        float: left;
        width: 50%;
        padding: 4px;
        font-weight: bolder;
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
        width: 15px;
        min-width: 15%;
    }

    .border_line_2 {
        width: 40px;
        min-width: 40%;
    }

    .page-break {
        page-break-inside: auto;
        page-break-after: always;

    }

    .column_25 {
        float: left;
        width: 25%;
    }
    .column_50 {
        float: left;
        width: 50%;
    }
    .column_50_detail {
        width: 50%;
    }

    .column_100 {
        width: 100%;
    }
    .column_100:after {
        content: "";
        display: table;
        clear: both;
    }

    .column_10 {
        float: left;
        width: 10%;
    }
    .column_30 {
        float: left;
        width: 30%;
    }
    .column_15 {
        float: left;
        width: 20%;
    }

    .column_35 {
        float: left;
        width: 35%;
    }

    .column_30 {
        float: left;
        width: 30%;
    }
    .column_20:after {
        width: 20%;
        content: "";
        clear: both;
    }

    .column_90 {
        float: left;
        width: 90%;
    }

</style>
<body>
    @yield('section')
</body>

</html>
