@extends('payment::layouts.master')

@section('content')
    <div class="flex flex-col justify-center items-center text-center text-3xl  shadow-md rounded-lg px-20 py-6">
        <div class="mb-4 flex flex-col items-center">
            <div class="flex justify-center">
                <svg class="text-red-600" width="50" height="50" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M9.16937 15.5801C8.97937 15.5801 8.78938 15.5101 8.63938 15.3601C8.34938 15.0701 8.34938 14.5901 8.63938 14.3001L14.2994 8.64011C14.5894 8.35011 15.0694 8.35011 15.3594 8.64011C15.6494 8.93011 15.6494 9.41011 15.3594 9.70011L9.69937 15.3601C9.55937 15.5101 9.35937 15.5801 9.16937 15.5801Z"
                        fill="currentColor" />
                    <path
                        d="M14.8294 15.5801C14.6394 15.5801 14.4494 15.5101 14.2994 15.3601L8.63938 9.70011C8.34938 9.41011 8.34938 8.93011 8.63938 8.64011C8.92937 8.35011 9.40937 8.35011 9.69937 8.64011L15.3594 14.3001C15.6494 14.5901 15.6494 15.0701 15.3594 15.3601C15.2094 15.5101 15.0194 15.5801 14.8294 15.5801Z"
                        fill="currentColor" />
                    <path
                        d="M15 22.75H9C3.57 22.75 1.25 20.43 1.25 15V9C1.25 3.57 3.57 1.25 9 1.25H15C20.43 1.25 22.75 3.57 22.75 9V15C22.75 20.43 20.43 22.75 15 22.75ZM9 2.75C4.39 2.75 2.75 4.39 2.75 9V15C2.75 19.61 4.39 21.25 9 21.25H15C19.61 21.25 21.25 19.61 21.25 15V9C21.25 4.39 19.61 2.75 15 2.75H9Z"
                        fill="currentColor" />
                </svg>

            </div>
            <div class="mt-4">

                <h2 class="text-lg  text-gray-700">
                    پرداخت نا موفق
                </h2>
                <div class="text-sm my-4 font-semibold">
                    کد پیگیری : {{ $data['reference_code'] }}
                </div>

            </div>
            <div class="mt-3">
                <a href="https://t.me/herofalcon_bot" class="text-sm bg-gray-100 px-6 py-2 text-center rounded-xl">بازگشت به
                    ربات</a>

            </div>


        </div>

    </div>
@endsection
