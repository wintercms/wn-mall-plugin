document.addEventListener("DOMContentLoaded", () => {
    restoreBtn = document.querySelector('button[data-request="onRestoreSelected"]')
    showDeletedFilter = document.getElementById('listFilter-scope-showDeleted')

    if (showDeletedFilter) {
        setButtonClass(restoreBtn, showDeletedFilter.checked)
        showDeletedFilter.addEventListener('change', (event) => {
            setButtonClass(restoreBtn, showDeletedFilter.checked)
        })
    }
})

function setButtonClass(btn, state)
{
    if (state) {
        btn.classList.remove('hidden')
    } else {
        btn.classList.add('hidden')
    }
}

function initializeSorting () {
    if (typeof Sortable === 'undefined') return;
    var $tbody = $('.drag-handle').parents('table.data tbody');
    if (!$tbody.length) {
        return
    }
	$tbody.each(function () {
		var data = {};
		var field = this.closest('div.form-group[data-field-name]');

		if (field) {
			data.fieldName = field.dataset.fieldName;
		}
		Sortable.create(this, {
			handle: '.drag-handle',
			animation: 150,
			onEnd: function (evt) {
				var $inputs = $(evt.target).find('td>div.drag-handle>input');
				var $form = $('<form style="display: none;">');
				$form.append($inputs.clone())
					.request('onReorderRelation', {
						data: data,
						complete: function () {
							$form.remove();
						}
					});
			}
		});
	});
}

$(function () {
    initializeSorting();
    $(window).on('ajaxUpdateComplete', initializeSorting)
});
