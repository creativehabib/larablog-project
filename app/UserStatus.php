<?php

namespace App;

enum UserStatus: String
{
    case Pending = "pending";
    case Active = "active";
    case Inactive = "inactive";
    case Rejected = "rejected";

}
