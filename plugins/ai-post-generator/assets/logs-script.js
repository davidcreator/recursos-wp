(function($) {
    'use strict';

    $(document).ready(function() {
        const $viewer = $('#aipg-logs-viewer');
        const $search = $('#aipg-logs-search');
        const $level = $('#aipg-logs-level');
        const originalContent = $viewer.text();

        // Atualizar logs
        $('#aipg-logs-refresh').on('click', function() {
            location.reload();
        });

        // Limpar logs
        $('#aipg-logs-clear').on('click', function() {
            if (confirm('Tem certeza que deseja limpar todos os logs?')) {
                $.post(ajaxurl, {
                    action: 'aipg_clear_logs',
                    nonce: $('input[name="_wpnonce"]').val()
                }, function(response) {
                    if (response.success) {
                        alert('Logs limpos com sucesso');
                        location.reload();
                    }
                });
            }
        });

        // Baixar logs
        $('#aipg-logs-download').on('click', function() {
            const content = $viewer.text();
            const blob = new Blob([content], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'aipg-logs-' + new Date().toISOString().split('T')[0] + '.log';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        });

        // Filtrar logs em tempo real
        $search.on('keyup', function() {
            filterLogs();
        });

        $level.on('change', function() {
            filterLogs();
        });

        function filterLogs() {
            const searchText = $search.val().toLowerCase();
            const levelFilter = $level.val();
            const lines = originalContent.split('\n');
            const filtered = lines.filter(line => {
                const matchesSearch = !searchText || line.toLowerCase().includes(searchText);
                const matchesLevel = !levelFilter || line.toUpperCase().includes(levelFilter);
                return matchesSearch && matchesLevel;
            });

            $viewer.text(filtered.join('\n'));
        }
    });

})(jQuery);