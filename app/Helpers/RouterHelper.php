<?

namespace App\Helpers;

class RouterHelper 
{
    static function returnRequest(int $status, array $message): string {
        http_response_code($status);
        return json_encode($message);
    }
}

