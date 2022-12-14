<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" type="image/x-icon" href="/images/credit-card-fill.svg">
        <link href="/css/main.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="/js/main.js" rel="stylesheet"></script>
        <title>BudgetMaster</title>
    </head>
    <body>
        <nav>
            <div>
                <a href="/"><i class="bi bi-credit-card-fill"></i> BudgetMaster</a>
            </div>
            <div class="dropdown">
                <button class="dropbtn">user@uniba.sk <i class="bi bi-caret-down-fill"></i></button>
                <div class="dropdown-content">
                    <a class="change-pass">Zmena hesla</a>
                    <a class="create-user">Vytvoriť používateľa</a>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" id="logout-button">Odhlásiť sa</button>
                    </form>
                </div>
            </div>
        </nav>
        <div class="content">

            @include('auth.modals.create_user')
            @include('auth.modals.change_password')

            @include('finances.modals.operation')
            @include('finances.modals.create_operation')
            @include('finances.modals.edit_operation')
            @include('finances.modals.check_operation')
            @include('finances.modals.delete_operation')

            @include('finances.modals.add_report')

            @include('finances.modals.create_account')
            @include('finances.modals.edit_account')
            @include('finances.modals.delete_account')
