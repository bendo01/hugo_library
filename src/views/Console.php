<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Views;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/** Shared terminal I/O used by every view. */
class Console
{
    private static ?OutputInterface $output = null;
    private static ?ArgvInput $input = null;

    public static function output(): OutputInterface
    {
        return self::$output ??= new ConsoleOutput();
    }

    public static function table(array $headers, array $rows, string $title = ''): void
    {
        $table = new Table(self::output());
        $table->setHeaders($headers)->setRows($rows);
        if ($title !== '') {
            $table->setHeaderTitle($title);
        }
        $table->render();
    }

    public static function success(string $message): void
    {
        self::output()->writeln("<info>✔ {$message}</info>");
    }

    public static function error(string $message): void
    {
        self::output()->writeln("<error> {$message} </error>");
    }

    public static function info(string $message): void
    {
        self::output()->writeln($message);
    }

    public static function ask(string $label, ?string $default = null): ?string
    {
        $suffix = $default !== null && $default !== '' ? " [{$default}]" : '';
        $answer = self::helper()->ask(self::$input ??= new ArgvInput([]), self::output(), new Question("{$label}{$suffix}: ", $default));

        return $answer === null ? null : trim((string) $answer);
    }

    public static function confirm(string $label): bool
    {
        return (bool) self::helper()->ask(
            self::$input ??= new ArgvInput([]),
            self::output(),
            new ConfirmationQuestion("{$label} (y/N) ", false)
        );
    }

    public static function pause(): void
    {
        self::ask('Press Enter to continue');
    }

    private static function helper(): QuestionHelper
    {
        return new QuestionHelper();
    }
}
