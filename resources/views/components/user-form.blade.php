<!-- Modal Form -->
<div class="modal fade modal-lg" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add Color</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <!-- Client Name -->
                    <div class="mb-3">
                        <label class="form-label" for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="name" name="name" class="form-control" placeholder="Enter User Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email<span class="text-danger">*</span></label>
                        <input type="email" maxlength="100" id="email" name="email" class="form-control" placeholder="user@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="password" name="password" class="form-control" placeholder="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">Conform Password<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="conformPassword Password" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>