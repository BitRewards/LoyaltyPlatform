<?php
/**
 * @var \App\Models\User $user
 */
?>
@extends('emails.templates.notification.notification', [
    'logo' => [
        'path' => HCustomizations::logoPicture($user->partner),
        'height' => 120,
    ]
])

@section('content')
    @include ('emails.templates.notification.heading' , [
        'heading' => 'Промо-код <span>OBOROT</span> на скидку 5000 рублей на продукты <span>BitRewards</span>',
        'level' => 'h1',
    ])

    @include('emails.templates.notification.contentStart')
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                <p>
                    Добрый день.<br>
                    Спасибо, что заинтересовались продуктами BitRewards!
                </p>
                <br><p>
                    Вы уже получили скидку 5000 рублей на инструменты BitRewards при оплате от 6 месяцев. Назовите промо-код OBOROT нашему менеджеру. Предложение действует до 31.12.2018.
                </p>
                <br><p>
                    <a href="//app.bitrewards.com/files/presentation_oborot.pdf">По ссылке</a> вы найдете презентацию всех продуктов BitRewards.
                </p>
                <br><p>
                    Менеджеры BitRewards с удовольствием проведут вам индивидуальную презентацию и помогут настроить продукты BitRewards для вашего проекта.
                </p>
                <br><p>
                    На какое число назначить презентацию?
                </p>
            </td>
        </tr>
        <tr><td><br></td></tr>
    @include('emails.templates.notification.button', [
        'title' => 'Назначить дату презентации',
        'link' => 'mailto:oborot@bitrewards.com?subject=Нужна презентация продукта BitRewards'
    ])
    </table>
    @include('emails.templates.notification.contentEnd')
@stop