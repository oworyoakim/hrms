<?php

use App\Models\PerformanceStandard;
use Illuminate\Database\Seeder;

class PerformanceStandardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // PerformanceStandard::query()->truncate();

        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Time Management and Planning'
        ],[
            'question' => 'Does the employee exhibit flexibility in planning and coordinating work; and adherence to schedules?',
            'excellent' => 'Has exceptional ability to balance priorities effectively; consistently grasps what takes precedence and works on what is most urgently needed; meets schedules and deadlines',
            'veryGood' => 'Has a very good grasp of priorities and what should take precedence; responds to most urgent priorities first; usually meets deadlines and schedules',
            'good' => 'Meets most expectations of setting priorities with some consultation; and may need assistance in coordinating work-plans, scheduling and deadlines',
            'satisfactory' => 'Meets deadlines with minimal supervision; does not grasp how to prioritise',
            'unsatisfactory' => 'Fails to meet deadlines or schedules; cannot manage time',
        ]);
        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Job Knowledge'
        ],[
            'question' => 'Does the employee have the knowledge to do the job effectively?',
            'excellent' => 'Has an exceptionally thorough grasp of all facets of the job; understands the direction of the Company, the office and the job',
            'veryGood' => 'Has a good grasp of the job; rarely requires assistance.',
            'good' => 'Has grasp of the job; requires minimal supervision and some training.',
            'satisfactory' => 'Has little grasp of the job. Needs Improvement to perform the job; requires supervision and additional training.',
            'unsatisfactory' => 'Lacks knowledge and skills to effectively perform duties and responsibilities of the job. Training required may be excessive',
        ]);
        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Creativity'
        ],[
            'question' => 'How well Does the employee champion innovations and create more value for the Company?',
            'excellent' => 'Exceptionally innovative and always works towards adding value to the Company',
            'veryGood' => 'Highly innovative and tries to add value to Company activities',
            'good' => 'Fairly innovative and works hard to add value to the Company',
            'satisfactory' => 'Hardly innovative without assistance. Usually depends on others for direction',
            'unsatisfactory' => 'Overwhelmed by problems; shows no initiative and cannot formulate acceptable solutions; always reactive',
        ]);
        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Dependability and Commitment'
        ],[
            'question' => 'How well does the employee follow procedures, directives and policies without supervision?',
            'excellent' => 'Highly dependable and works without supervision;',
            'veryGood' => 'Always thoroughly reliable without supervision',
            'good' => 'Requires minimal supervision',
            'satisfactory' => 'Requires supervision occasionally',
            'unsatisfactory' => 'Requires constant supervision',
        ]);
        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Client Service'
        ],[
            'question' => 'How well does the employee attend to client’s needs and problems within agreed time frame?',
            'excellent' => 'Exceptional service to clients; exceptionally friendly and helpful towards clients.',
            'veryGood' => 'Very Good service to clients; friendly and helpful towards clients',
            'good' => 'Largely meets service expectations of clients.',
            'satisfactory' => 'Strives to meet client’s expectations',
            'unsatisfactory' => 'Rarely attends to client’s needs. Often rude and offensive towards clients',
        ]);

        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Judgement'
        ],[
            'question' => 'How well does the employee analyse facts, consider related impact, and arrive at sound conclusions?',
            'excellent' => 'Exceptional ability to analyse the facts, considers related impact and arrives at sound conclusions; consistently reflects discretion and good taste always',
            'veryGood' => 'Most often analyses the facts, considers impacts and reaches sound conclusions; frequently reflects discretion and good taste and discernment.',
            'good' => 'Exhibits reasonable ability to analyse, draw inferences, reach judgements and demonstrate discernment and good taste',
            'satisfactory' => 'Seldom reflects the ability to analyse, draw inferences, reach judgements and exercise discretion; seldom demonstrates discernment',
            'unsatisfactory' => 'Demonstrates almost no ability to analyse, draw inferences, reach judgements, and reflect discretion and good taste',
        ]);

        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Teamwork'
        ],[
            'question' => 'How well does the employee work with others to accomplish the goals of the job and work group?',
            'excellent' => 'Works extremely well with others and responds enthusiastically to new challenges',
            'veryGood' => 'Very Co-operative and flexible',
            'good' => 'Usually gets along reasonably well but occasionally uncooperative',
            'satisfactory' => 'Generally Unco-operative, and resists change',
            'unsatisfactory' => 'Always uncooperative   to work with others and does not contribute to team goals',
        ]);

        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Attendance & Punctuality'
        ],[
            'question' => 'What is the employee’s pattern of absence and punctuality?',
            'excellent' => 'Always on time for work and/or appointments and remains at work as needed. Rarely absent',
            'veryGood' => 'Very Good attendance levels and is rarely late',
            'good' => 'Good attendance levels and generally punctual',
            'satisfactory' => 'Absence and or lateness levels are higher than average.',
            'unsatisfactory' => 'Frequently late and/or absent',
        ]);

        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Interpersonal Relations'
        ],[
            'question' => 'Does the employee Work well with others; Accept direction; Contribute positively to the team; Respond appropriately to feedback.',
            'excellent' => 'Consistently displays  exceptional ability toward working with others and toward working with others and toward their work; excellent team player who actively creates good will',
            'veryGood' => 'Displays enthusiasm toward working with others and toward their work; exercises tact and discretion; a good team player; courteous',
            'good' => 'Displays genuine interest in working with others and in their work; cooperative and pleasant; creates a favorable impression',
            'satisfactory' => 'Displays marginal interest in working with others and their work;occassionally un cooperative, unfriendly, curt or irritable; sometimes has negative effect on others',
            'unsatisfactory' => 'Indifferent; exhibits no interest in working with others and their work. Defensive and argumentative. Cooperates only when forced to and has negative effect on others.',
        ]);

        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Communication'
        ],[
            'question' => 'How effective is the employee at verbal and written communication?',
            'excellent' => 'Exceptionally effective in all written and verbal communication',
            'veryGood' => 'Communication both oral and written is consistently concise and understandable',
            'good' => 'Meets expectations of oral and written communication in a concise and logical way',
            'satisfactory' => 'Oral and written communication is sometimes confusing and misunderstood by others',
            'unsatisfactory' => 'Both verbal and written communication is unacceptable in terms of clarity, expression and presentation.',
        ]);

        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Integrity/Honesty'
        ],[
            'question' => 'Does the employee  Adhere  to high standards of personal and professional conduct',
            'excellent' => 'Exceedingly demonstrates and upholds Company values, as stipulated in the Strategic plan; acts without consideration of personal gain and does not abuse power and authority',
            'veryGood' => 'Employee adheres to required Company values and regulations.  Maintains a high Level of character and professional attitude.',
            'good' => 'Employee complies with Company policies, procedures, and standards of conduct most of the time.',
            'satisfactory' => 'Occasionally engages in questionable behavior and at times is insensitive to uprightness',
            'unsatisfactory' => 'Unethical, insensitive to uprightness and does not comply with Company policies, procedures, and standards of conduct regulations.',
        ]);

        PerformanceStandard::query()->updateOrCreate([
            'factor' => 'Confidentiality'
        ],[
            'question' => 'Can the employee be trusted to use discretion in dealing with clients and fellow employees; maintain secrecy of information or materials appropriate to position?',
            'excellent' => 'Exceptionally respects the confidentiality of information acquired and, does not disclose any such information to third parties without proper and specific authority, nor use the information for his/her personal advantage',
            'veryGood' => 'Consistently promotes atmosphere of confidentiality through continuous monitoring and communication of confidentiality standards. Actively discourages office gossip about clients/customers and/or fellow employees',
            'good' => 'Maintains the confidentiality of all appropriate records or materials. Uses discretion in dealing with all clients/customers and/or fellow employees. Does not participate in office gossip concerning clients/customers and/or fellow employees',
            'satisfactory' => 'Demonstrates lack of concern for confidentiality through behavior or conversation. Participates in office gossip with little regard to potential negative consequences',
            'unsatisfactory' => '',
        ]);

    }
}
