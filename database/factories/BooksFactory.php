<?php

namespace Database\Factories;
use App\Models\Books;
use App\Models\Dojo;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Books>
 */
class BooksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Books::class;
    public function definition(): array
    {
        $section = fake()->randomElement(['CICS', 'CTED', 'CCJE', 'CHM', 'CBEA', 'CA']);
    
        $lastBook = Books::where('section', $section)->orderBy('bookID', 'desc')->first();
        $nextNumber = $lastBook ? (int) substr($lastBook->bookID, -2) + 1 : 1; 
    
        $bookID = strtoupper($section) . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    
        $bookTitles = [
            'CICS' => [
                'The Art of Programming: From Basics to Mastery',
                'Cybersecurity in the Digital Age',
                'Artificial Intelligence and Machine Learning Essentials',
                'Cloud Computing: Concepts and Applications',
                'Software Engineering Best Practices',
            ],
            'CTED' => [
                'Innovative Teaching Strategies for the 21st Century',
                'The Science of Learning and Development',
                'Educational Psychology in Practice',
                'Assessment and Evaluation in Education',
                'Classroom Management for Effective Teaching',
            ],
            'CCJE' => [
                'Criminal Law & Procedures: A Practical Approach',
                'Forensic Science and Criminal Investigations',
                'The Psychology of Crime and Criminal Behavior',
                'Modern Policing and Law Enforcement Strategies',
                'Human Rights and Ethics in Criminal Justice',
            ],
            'CHM' => [
                'Hospitality and Tourism Management: Trends and Practices',
                'Customer Service Excellence in the Hospitality Industry',
                'Food and Beverage Management Essentials',
                'Event Planning and Hospitality Operations',
                'Sustainable Tourism and Hotel Management',
            ],
            'CBEA' => [
                'Financial Accounting: Principles and Applications',
                'Managerial Accounting for Decision Making',
                'Auditing and Assurance Services',
                'Taxation Laws and Business Compliance',
                'Corporate Finance and Investment Strategies',
            ],
            'CA' => [
                'Sustainable Farming and Modern Agriculture Techniques',
                'Soil Science and Crop Production',
                'Agribusiness Management and Marketing',
                'Livestock and Poultry Farming',
                'Organic Farming and Sustainable Food Production',
            ],
        ];
    
        return [
            'bookID' => $this->faker->unique()->bothify('BOOK###'),
            'name' => $this->faker->sentence(3),
            'author' => $this->faker->name,
            'section' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'dojo_id' => Dojo::inRandomOrder()->first()->id ?? Dojo::factory()->create()->id, // Ensure dojo_id exists
        ];
    }
    
}
