<?php

namespace App\Enums;

enum LanguageSwitchPlacement: string
{
    case TopLeft = 'top-left';

    case TopCenter = 'top-center';

    case TopRight = 'top-right';

    case BottomLeft = 'bottom-left';

    case BottomCenter = 'bottom-center';

    case BottomRight = 'bottom-right';
}
