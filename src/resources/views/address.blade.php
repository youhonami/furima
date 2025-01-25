<form action="{{ route('address.update') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="postal_code">郵便番号</label>
        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', session('temp_address.postal_code') ?? $profile->postal_code ?? '') }}" placeholder="郵便番号を入力してください">
    </div>

    <div class="form-group">
        <label for="address">住所</label>
        <input type="text" id="address" name="address" value="{{ old('address', session('temp_address.address') ?? $profile->address ?? '') }}" placeholder="住所を入力してください">
    </div>

    <div class="form-group">
        <label for="building">建物名</label>
        <input type="text" id="building" name="building" value="{{ old('building', session('temp_address.building') ?? $profile->building ?? '') }}" placeholder="建物名を入力してください">
    </div>

    <button class="update-button">更新する</button>
</form>