<!-- resources/views/components/instructor-fields.blade.php -->

<div class="form-group col-sm-6">
    <!-- Name Field -->
    <x-input-text label="Name" name="name" :value="$Instructor->name"/>
</div>

<div class="form-group col-sm-6">
    <!-- Display Name Field -->
    <x-input-text label="Display Name" name="display_name" :value="$Instructor->display_name"/>
</div>

<div class="form-group col-sm-6">
    <!-- Pronouns Field -->
    <x-input-text label="Pronouns" name="pronouns" :value="$Instructor->pronouns"/>
</div>

<div class="form-group col-sm-6">
    <!-- Email Field -->
    <x-input-email label="Email" name="email" :value="$Instructor->email"/>
</div>

<div class="form-group col-sm-6">
    <!-- Phone Field -->
    <x-input-text label="Phone" name="phone" :value="$Instructor->phone"/>
</div>
