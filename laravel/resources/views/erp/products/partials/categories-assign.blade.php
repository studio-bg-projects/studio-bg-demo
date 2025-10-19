@php($categories = $categories ?? [])

@php($assigned = $assigned ?? [])

@php($level = $level ?? 0)

@if ($categories)
  @foreach($categories as $category)
    <option value="{{ $category->id }}" @if (in_array($category->id, $assigned)) selected @endif class="@if (!$category->isActive) opacity-50 @endif @if($errors->has('categories.' . $category->id)) text-danger @endif">
      {!! str_repeat('&nbsp; &nbsp; ', $level) !!}
      {{ $category->nameBg }}
    </option>
    @if ($category->children->isNotEmpty())
      @include('erp.products.partials.categories-assign', [
        'categories' => $category->children,
        'assigned' => $assigned,
        'level' => $level + 1,
      ])
    @endif
  @endforeach
@endif
