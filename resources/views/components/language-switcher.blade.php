<form method="POST" action="{{ route('set-locale') }}" class="inline">
    @csrf
    <select name="locale" onchange="this.form.submit()" class="border rounded p-1">
        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
        <option value="de" {{ app()->getLocale() === 'de' ? 'selected' : '' }}>Deutsch</option>
    </select>
</form>