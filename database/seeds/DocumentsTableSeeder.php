<?php

use App\Models\Company;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DocumentCategory::query()->truncate();
        DocumentType::query()->truncate();

        if($category = DocumentCategory::create([
            'title' => 'Qualifications',
        ])){
            DocumentType::create([
                'title' => 'PLE',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'UCE',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'UACE',
                'category_id' => $category->id
            ]);

            DocumentType::create([
                'title' => 'Certificate',
                'category_id' => $category->id
            ]);

            DocumentType::create([
                'title' => 'Diploma Transcript',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Diploma Certificate',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Bachelor Transcript',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Bachelor Certificate',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'PGD Certificate',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'PDG Transcript',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Masters Transcript',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Masters Certificate',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Doctorate',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Professional Certifications',
                'category_id' => $category->id
            ]);
        }

        if($category = DocumentCategory::create([
            'title' => 'Marital',
        ])){
            DocumentType::create([
                'title' => 'Traditional Marriage Certificate',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Church Marriage Certificate',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Civil Marriage Certificate',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Divorce Certificate',
                'category_id' => $category->id
            ]);
        }

        if($category = DocumentCategory::create([
            'title' => 'Birth',
        ])){
            DocumentType::create([
                'title' => 'Birth Certificate',
                'category_id' => $category->id
            ]);
        }
        if($category = DocumentCategory::create([
            'title' => 'Identity Document',
        ])){
            DocumentType::create([
                'title' => 'National ID',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Passport',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Driving License',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'NSSF',
                'category_id' => $category->id
            ]);
        }
        if($category = DocumentCategory::create([
            'title' => 'Publications',
        ])){
            DocumentType::create([
                'title' => 'Books',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Articles',
                'category_id' => $category->id
            ]);
            DocumentType::create([
                'title' => 'Research Papers',
                'category_id' => $category->id
            ]);
        }
        if($category = DocumentCategory::create([
            'title' => 'Governance',
            'non_employee' => true,
        ])){
            DocumentType::create([
                'title' => 'Policy Document',
                'category_id' => $category->id,
            ]);
            DocumentType::create([
                'title' => 'Acts of Parliament',
                'category_id' => $category->id,
            ]);
            DocumentType::create([
                'title' => 'Corporate Structure',
                'category_id' => $category->id,
            ]);
            DocumentType::create([
                'title' => 'Strategy Document',
                'category_id' => $category->id
            ]);
        }
        if($category = DocumentCategory::create([
            'title' => 'Projects',
            'non_employee' => true,
        ])){
            DocumentType::create([
                'title' => 'Reports',
                'category_id' => $category->id,
            ]);
        }

        if($category = DocumentCategory::create([
            'title' => 'Employment',
        ])){
            DocumentType::create([
                'title' => 'Appointment',
                'category_id' => $category->id,
            ]);
            DocumentType::create([
                'title' => 'Dismissal',
                'category_id' => $category->id,
            ]);
            DocumentType::create([
                'title' => 'Notice',
                'category_id' => $category->id,
            ]);
            DocumentType::create([
                'title' => 'Leave Document',
                'category_id' => $category->id,
            ]);
        }
    }
}
