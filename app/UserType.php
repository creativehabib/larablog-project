<?php

namespace App;

enum UserType: string
{
    case SuperAdmin = 'superadmin';
    case Administrator = 'administrator';
    case Editor = 'editor';
    case Author = 'author';
    case Contributor = 'contributor';
    case Subscriber = 'subscriber';
}
