{{-- Filters the subject dropdown to subjects the selected teacher actually
     teaches in the selected class (per class_subjects), reused by create/edit. --}}
@push('scripts')
<script>
(function () {
    var teacherSelect = document.getElementById('teacher_id');
    var classSelect   = document.getElementById('class_id');
    var subjectSelect = document.getElementById('subject_id');
    if (!teacherSelect || !classSelect || !subjectSelect) return;

    var classSubjects  = @json($classSubjects->values());
    var allSubjects     = @json($subjects->map(fn ($s) => ['id' => $s->id, 'name' => $s->name_ar])->values());
    var initialSubjectId = '{{ old('subject_id', $educationalNote->subject_id ?? '') }}';

    function refreshSelect2(select) {
        if (window.jQuery && jQuery(select).data('select2')) {
            jQuery(select).select2('destroy');
        }
        if (window.initSelect2) window.initSelect2(select);
    }

    function applyFilter() {
        var teacherVal = teacherSelect.value;
        var classVal   = classSelect.value;
        var currentVal = subjectSelect.value;

        var allowedIds = null; // null = no filter, show every subject
        if (teacherVal || classVal) {
            allowedIds = new Set(
                classSubjects
                    .filter(function (cs) {
                        return (!teacherVal || String(cs.teacher_id) === teacherVal)
                            && (!classVal || String(cs.class_id) === classVal);
                    })
                    .map(function (cs) { return String(cs.subject_id); })
            );
        }

        subjectSelect.innerHTML = '';
        var placeholder = new Option('— {{ __('messages.select_subject') }} —', '');
        subjectSelect.appendChild(placeholder);

        allSubjects.forEach(function (s) {
            var id = String(s.id);
            var isAllowed  = allowedIds === null || allowedIds.has(id);
            // Always keep the value that's currently selected (or the note's
            // saved subject) visible, even if it falls outside the filter —
            // avoids silently dropping existing data.
            var mustKeep = id === currentVal || id === initialSubjectId;
            if (isAllowed || mustKeep) {
                var opt = new Option(s.name, id);
                if (id === currentVal) opt.selected = true;
                subjectSelect.appendChild(opt);
            }
        });

        refreshSelect2(subjectSelect);
    }

    teacherSelect.addEventListener('change', applyFilter);
    classSelect.addEventListener('change', applyFilter);
    applyFilter();
})();
</script>
@endpush
