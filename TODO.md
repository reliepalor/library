# TODO: Attendance Table Enhancement

## Completed Tasks
- [x] Updated User AttendanceController to fetch teacher attendances separately
- [x] Modified the view to display teacher attendances initially with Role, Profile Picture, Name, and Department
- [x] Fixed JavaScript to properly handle profile picture URLs for teachers
- [x] Added borrower type indicators (Student/Teacher/Visitor) in admin borrow requests view

## Summary of Changes
- **Controller**: Added separate query for teacher attendances in the `index()` method
- **View**: Added Blade template rendering for teacher attendances with proper data display
- **JavaScript**: Fixed profile picture URL construction to use storage base path
- **Admin Borrow Requests**: Added visual indicators showing whether borrowers are students or teachers/visitors

## Testing Needed
- [x] Verify teacher attendance records display correctly on page load
- [x] Check that profile pictures load properly for teachers
- [x] Ensure real-time updates work for both student and teacher tables
- [x] Test responsive design on mobile devices
- [x] Verify borrower type indicators appear correctly in admin borrow requests
- [x] Added fallback error handling for profile pictures in user attendance view
- [x] Fixed teacher profile picture display by adding AvatarService fallback in controller and handling URL paths in JavaScript
- [x] Simplified profile picture URL handling in JavaScript to use direct URLs from controller

## Study Area Slot Management Implementation
- [x] Add `isStudyActivity` method to `StudyAreaHelper`
- [x] Modify `User/AttendanceController::log()` to manage study slots on login/logout
- [x] Modify `Admin/AttendanceController::log()` to manage study slots on login/logout
- [ ] Test study slot decrement on login with study activities
- [ ] Test study slot increment on logout from study activities
- [ ] Verify non-study activities don't affect slots

## Notes
- Teachers and visitors are now displayed with their Role, Profile Picture, Name, and Department as requested
- The table shows Activity, Login, and Logout times
- Real-time updates are handled via JavaScript polling every 3 seconds
- Admin borrow requests now clearly show borrower type with color-coded badges
