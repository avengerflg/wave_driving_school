<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
        [
            'name' => '5 Hour Lesson Package',
            'description' => "Embark on the path to driving proficiency with Wave Driving School's 5-Hour Lesson Package! Specifically designed for beginners and those seeking to improve their driving skills, this all-inclusive package addresses crucial elements of safe and confident driving. Our certified instructor offers personalized guidance, encompassing essential skills from basic maneuvers to road etiquette.\n\nEnhance your confidence on the road and establish a solid foundation for a lifetime of secure driving. Enroll in our 5-Hour Lesson Package today!",
            'price' => 360.00,
            'duration' => 300, // minutes
            'active' => true,
        ],
        [
            'name' => '10 Hour Lesson Package',
            'description' => "Introducing our 10-Hour Driving Lesson Package offered by Wave Driving School! This comprehensive package is designed to provide beginner and intermediate drivers with the necessary skills and confidence to navigate the road safely.\n\nDuring these 10 hours, we cover a range of driving topics, including vehicle controls and operations, road signs and markings, defensive driving strategies, parking maneuvers, and more. Our instructor is experienced in creating a supportive and encouraging learning environment, ensuring that students feel comfortable and at ease throughout their lessons.",
            'price' => 700.00,
            'duration' => 600,
            'active' => true,
        ],
        [
            'name' => '20 Hour Lesson Package',
            'description' => "Introducing the 20-Hour Driving Lesson Package offered by Wave Driving School, exclusively with our experienced instructor, Jasbir! This comprehensive package is perfect for those looking to enhance their driving skills and gain the confidence needed to navigate the road with ease.\n\nDon't delay your journey towards becoming a skilled and responsible driver. Book the 20-Hour Driving Lesson Package with Jasbir at Wave Driving School today and experience the difference our expert instruction can make in your driving abilities.",
            'price' => 1400.00,
            'duration' => 1200,
            'active' => true,
        ],
        [
            'name' => '1 Hour Driving Lesson',
            'description' => "Embark on a confident journey behind the wheel with Wave Driving School's 1-Hour Auto Driving Lesson, led by our experienced instructor, Jasbir. Perfect for beginners or those looking to refine their skills, this personalized session covers essential driving techniques, road safety, and hands-on practice in a supportive environment. Gain valuable insights, boost your confidence, and set the foundation for safe and skilled driving with Wave Driving School.",
            'price' => 75.00,
            'duration' => 60,
            'active' => true,
        ],
        [
            'name' => '1 Hour Car Hire',
            'description' => "Experience the convenience of our 1-Hour Car Hire service at Wave Driving School, designed to provide a flexible and personalized driving experience. Whether you're a beginner looking to build confidence behind the wheel or seeking a quick refresher, our expert instructor ensures a tailored session to meet your specific needs. With our modern and well-maintained fleet, you'll enjoy a comfortable and safe learning environment, gaining valuable skills to navigate the road with confidence.\n\nTake the wheel with Wave Driving School and embark on a journey towards driving success in just one hour!",
            'price' => 130.00,
            'duration' => 60,
            'active' => true,
        ],
        [
            'name' => 'Driving Test Pack',
            'description' => "Our experienced instructor understands the requirements and expectations of the driving test, and he will work closely with you to fine-tune your skills and build your confidence behind the wheel. The Driving Test Pack includes a series of lessons and mock tests that simulate the actual driving test, giving you practical experience and preparing you for what to expect on the big day.",
            'price' => 200.00,
            'duration' => 120,
            'active' => true,
        ],
    ];

    foreach ($services as $service) {
        Service::create($service);
    }
    }
}
