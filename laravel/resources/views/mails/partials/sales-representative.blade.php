@if ($salesRepresentative)
  <h3 style="margin-top: 20px;">
    {{ !empty($title) ? $title : [
      'bg' => 'Вашият търговски представител:',
      'en' => 'Your sales representative:'
    ][$lang] }}
  </h3>
  <p>
    <strong>{{ ['bg' => 'Име', 'en' => 'Name'][$lang] }}:</strong>
    {{ [
      'bg' => $salesRepresentative->nameBg,
      'en' => $salesRepresentative->nameEn
    ][$lang] }}
  </p>

  <p>
    <strong>{{ ['bg' => 'Длъжност', 'en' => 'Position'][$lang] }}:</strong>
    {{ [
      'bg' => $salesRepresentative->titleBg,
      'en' => $salesRepresentative->titleEn
    ][$lang] }}
  </p>

  @if ($salesRepresentative->phone1)
    <p>
      <strong>{{ ['bg' => 'Телефон', 'en' => 'Phone'][$lang] }}:</strong>
      {{ $salesRepresentative->phone1 }}
    </p>
  @endif

  @if ($salesRepresentative->phone2)
    <p>
      <strong>{{ ['bg' => 'Телефон - допълнителен', 'en' => 'Telephone - additional'][$lang] }}:</strong>
      {{ $salesRepresentative->phone2 }}
    </p>
  @endif

  @if ($salesRepresentative->email1)
    <p>
      <strong>{{ ['bg' => 'Имейл', 'en' => 'Email'][$lang] }}:</strong>
      <a href="mailto:{{ $salesRepresentative->email1 }}" style="color: #007BFF; text-decoration: none;">{{ $salesRepresentative->email1 }}</a>
    </p>
  @endif

  @if ($salesRepresentative->phone2)
    <p>
      <strong>{{ ['bg' => 'Имейл - допълнителен', 'en' => 'Email - additional'][$lang] }}:</strong>
      <a href="mailto:{{ $salesRepresentative->email2 }}" style="color: #007BFF; text-decoration: none;">{{ $salesRepresentative->email2 }}</a>
    </p>
  @endif
@endif
