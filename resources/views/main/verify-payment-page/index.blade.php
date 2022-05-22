@extends('main.verify-payment-page.empty')

@section('custom-styles')
    <style>
        .register_panel--section {
            display: flex;
            margin: 30px 0 0 0;
        }

        .register_panel--section_item{
            display: flex;
            width: calc(25% - 20px);
            margin: 0 20px;
            align-items: center;
            flex-direction: column;
            background: #3f3f3f;
            padding: 15px 10px;
            color: #fff;
            box-sizing: content-box;
            border: 3px solid #fabc05;
            border-radius: 15px;
        }

        .register_panel--section_item a{
            background: #000;
            color: #fff;
            padding: 10px 15px;
        }

        p.register_panel-description {
            text-align: center;
            line-height: 49px;
        }

        a.register_panel-btn {
            background: #fabc05;
            color: #3f3f3f;
        }

        .register_panel-price {
            margin: 20px 0 0 0;
            display: inline-flex;
        }
    </style>
@endsection

@section('title', 'آنیف - نتیجه تراکنش')

@section('content')

    <div style="text-align: center;display: flex;align-items: center;justify-content: center;font-size: 25px;height:60vh;">



    @if($response['Status'] === 100 || $response['Status'] === 101)

        <p style='padding:10px 15px;background-color:#27ae60;display: flex;justify-content: center;align-items:center;color:#fff;'>
            {{$response['Message']}}
            @if(isset($response['explain']))
                <br><br>
                {{$response['explain']}}
            @endif
                <br>
                شماره پیگیری :{{$response['RefID']}}
            </p>

    @elseif($response['Status'] != 100)
        <p style='padding:10px 15px;background-color:#e74c3c;display: flex;justify-content: center;align-items:center;color:#fff;'>
            {{$response['Message']}}
            @if(isset($response['explain']))
                <br><br>
                {{$response['explain']}}
            @endif
            <br>
            @if(isset($response['RefID']))
            شماره پیگیری :{{$response['RefID']}}
                @endif
            </p>

    @endif

    <div style="clear:both;" class="clear">

    </div>

    </div>
@endsection