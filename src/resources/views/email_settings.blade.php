@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('parts.sidebar')

            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <h3><strong>Please setup mailing settings</strong></h3>
                        <h4>Step 1. Got SMTP details</h4>
                        <p>As you know to send any email you need have access to mailing service.</p>
                        <p>It can be any from many, like <a href="https://www.digitalocean.com/community/tutorials/how-to-use-google-s-smtp-server" target="_blank">google</a>, <a href="https://www.mailjet.com/feature/smtp-relay/" target="_blank">mailjet</a> or <a href="https://help.sendinblue.com/hc/en-us/articles/209462765-What-is-SendinBlue-SMTP-" target="_blank">sendinblue</a> e.t.c.</p>
                        <p>No mater what you chose from, but you need few main access credentials for:</p>
                        <ul>
                            <li>email host</li>
                            <li>access port</li>
                            <li>username</li>
                            <li>password</li>
                            <li>and encryption if it uses</li>
                        </ul>
                        <br>
                        <h4>Step 2. Apply settings</h4>
                        <p>Inside this project root directory, inside <strong>src</strong> folder you can find 2 files: <strong>.env</strong> and <strong>.env.example</strong></p>
                        <p>It equal files, but you need change in both next lines (from 33 to 37)</p>
                        <p>and fill with your credentials (hostname, port, username,password) :</p>
                        <p>
                            <pre><code>
                                MAIL_HOST=hostname
                                MAIL_PORT=port
                                MAIL_USERNAME=username
                                MAIL_PASSWORD=password
                                MAIL_ENCRYPTION=tls
                            </code></pre>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
