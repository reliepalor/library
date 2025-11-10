# Bulk Registration for Teachers/Visitors - Implementation Plan

## Overview
Implement bulk registration functionality for teachers/visitors, similar to the existing student bulk creation. This includes file upload, parsing, record creation, QR code generation, and email sending.

## Steps
- [x] Create bulk_create.blade.php view for teachers/visitors
- [x] Add bulkCreate() method to TeacherVisitorController
- [x] Add bulkStore() method to TeacherVisitorController
- [x] Update routes in web.php for bulk routes
- [x] Add bulk create link to index.blade.php
- [x] Implement separate results modal for better UX
- [x] Add "See List" button to results modal
- [ ] Test file upload and parsing
- [ ] Verify QR code generation and email sending
- [ ] Handle errors and validation

## Dependent Files
- app/Http/Controllers/Admin/Auth/TeacherVisitorController.php
- routes/web.php
- resources/views/admin/teachers_visitors/bulk_create.blade.php (new)
- resources/views/admin/teachers_visitors/index.blade.php

## Followup Steps
- Test the bulk upload with sample files (Excel, CSV, PDF)
- Ensure QR codes are generated correctly
- Confirm emails are sent with QR codes
- Handle any validation or parsing errors
