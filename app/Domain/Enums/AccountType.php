<?

namespace App\Domain\Enums;

enum AccountType: int {
    case CHECKING = 1;
    case SAVINGS = 2;
    case PAYMENT = 3;
    case BUSINESS = 4;
    case JOINT = 5;
    case INVESTMENT = 6;
    case SALARY = 7;
    case STUDENT = 8;
    case GOVERNMENT = 9;
}