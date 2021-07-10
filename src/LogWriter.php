<?php

namespace R64Robots\HttpLogger;

use Illuminate\Http\Request;

interface LogWriter
{
    public function logRequest(Request $request);

    public function logResponse(Request $request, $response);
}
