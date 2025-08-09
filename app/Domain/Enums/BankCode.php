<?

namespace App\Domain\Enums;


enum BankCode: int
{
    case BANCO_DO_BRASIL = 1;
    case ITAU = 341;
    case BRADESCO = 237;
    case CAIXA = 104;
    case SANTANDER = 33;
    case NUBANK = 260;
    case INTER = 77;
    case BTG = 208;
    case SAFRA = 422;
    case ORIGINAL = 212;
}