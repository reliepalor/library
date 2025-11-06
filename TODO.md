# TODO: Modify Student QR Code Generation

## Tasks
- [ ] Create private method `generateCompositeQr` in StudentController.php to generate QR image with text overlay
- [ ] Update `store` method to use new composite QR generation
- [ ] Update `bulkStore` method to use new composite QR generation
- [ ] Update `show` method to use new composite QR generation
- [ ] Update `update` method to use new composite QR generation
- [ ] Update `generateStudentQr` method to use new composite QR generation
- [ ] Update `resendQrCode` method to use new composite QR generation
- [ ] Modify `print_qr.blade.php` to remove redundant text display since it's now in the image
- [ ] Test QR generation and display
- [ ] Verify email sending with new composite QR
- [ ] Ensure print functionality works
