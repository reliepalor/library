<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckBorrows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-borrows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check recent borrows and their associated users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking recent borrows...');

        $borrows = \App\Models\BorrowedBook::with(['student', 'book', 'teacherVisitor'])
            ->where(function($query) {
                $query->whereHas('student')
                      ->orWhereHas('teacherVisitor');
            })
            ->latest()
            ->take(5)
            ->get();

        foreach($borrows as $borrow) {
            $this->line('ID: ' . $borrow->id . ', Type: ' . $borrow->user_type . ', Student: ' . ($borrow->student ? $borrow->student->fname . ' ' . $borrow->student->lname : 'null') . ', Teacher: ' . ($borrow->teacherVisitor ? $borrow->teacherVisitor->fname . ' ' . $borrow->teacherVisitor->lname : 'null'));
        }

        $this->info('Checking teacher borrows specifically...');
        $teacherBorrows = \App\Models\BorrowedBook::where('user_type', 'teacher')->get();
        if($teacherBorrows->isEmpty()) {
            $this->warn('No teacher borrows found');
        } else {
            foreach($teacherBorrows as $borrow) {
                $tv = \App\Models\TeacherVisitor::where('email', $borrow->student_id)->first();
                $this->line('Teacher borrow ID: ' . $borrow->id . ', student_id: ' . $borrow->student_id . ', TeacherVisitor: ' . ($tv ? $tv->fname . ' ' . $tv->lname : 'null'));
            }
        }
    }
}
