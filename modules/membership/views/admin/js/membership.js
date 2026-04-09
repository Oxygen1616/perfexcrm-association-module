// Membership Module JavaScript
// Handles AJAX calls and modal interactions for the Membership module

$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Handle election dropdown changes for election results and vote list
    $('#election_filter').on('change', function() {
        var electionId = $(this).val();
        if (electionId) {
            loadElectionResults(electionId);
            loadVoteList(electionId);
        } else {
            $('#election_results_container').html('<p class="text-muted"><?= _l('membership_select_election') ?></p>');
            $('#vote_list_container').html('<p class="text-muted"><?= _l('membership_select_election') ?></p>');
        }
    });

    // Handle committee dropdown changes for committee details (if needed)
    $('#committee_filter').on('change', function() {
        var committeeId = $(this).val();
        if (committeeId) {
            loadCommitteeDetails(committeeId);
        } else {
            $('#committee_details_container').html('');
        }
    });
});

// Function to load member details in modal
function showMemberDetails(memberId) {
    $('#memberModalLabel').text('<?= _l('membership_loading') ?>...');
    $('#memberModalBody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');
    $('#memberModal').modal('show');

    $.get("<?= admin_url('membership/ajax_get_member') ?>/" + memberId, function(response) {
        if (response.success) {
            var member = response.member;
            $('#memberModalLabel').text(member.firstname + ' ' + member.lastname);
            $('#memberModalBody').html(`
                <p><strong><?= _l('membership_member_email') ?>:</strong> ${member.email || '-'}</p>
                <p><strong><?= _l('membership_membership_type') ?>:</strong> ${member.membership_type || '-'}</p>
                <p><strong><?= _l('membership_member_profession') ?>:</strong> ${member.profession || '-'}</p>
                <p><strong><?= _l('membership_graduation_year') ?>:</strong> ${member.graduation_year || '-'}</p>
                <p><strong><?= _l('membership_show_in_directory') ?>:</strong> ${member.show_in_directory ? '<?= _l('membership_yes') ?>' : '<?= _l('membership_no') ?>'}</p>
                <p><strong><?= _l('membership_status') ?>:</strong> ${ucfirst(member.status || '')}</p>
                ${member.notes ? `<p><strong><?= _l('membership_notes') ?>:</strong> ${member.notes}</p>` : ''}
            `);
        } else {
            $('#memberModalBody').html('<p class="text-danger">' + response.message + '</p>');
        }
    });
}

// Function to load position details in modal
function showPositionDetails(positionId) {
    $('#positionModalLabel').text('<?= _l('membership_loading') ?>...');
    $('#positionModalBody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');
    $('#positionModal').modal('show');

    $.get("<?= admin_url('membership/ajax_get_position') ?>/" + positionId, function(response) {
        if (response.success) {
            var position = response.position;
            $('#positionModalLabel').text(position.name || '<?= _l('membership_position') ?>');
            $('#positionModalBody').html(`
                <p><strong><?= _l('membership_position_description') ?>:</strong> ${position.description || '-'}</p>
            `);
        } else {
            $('#positionModalBody').html('<p class="text-danger">' + response.message + '</p>');
        }
    });
}

// Function to load board member details in modal
function showBoardMemberDetails(memberId) {
    $('#boardMemberModalLabel').text('<?= _l('membership_loading') ?>...');
    $('#boardMemberModalBody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');
    $('#boardMemberModal').modal('show');

    $.get("<?= admin_url('membership/ajax_get_board_member') ?>/" + memberId, function(response) {
        if (response.success) {
            var member = response.member;
            $('#boardMemberModalLabel').text(member.firstname + ' ' + member.lastname);
            $('#boardMemberModalBody').html(`
                <p><strong><?= _l('membership_position') ?>:</strong> ${member.position || '-'}</p>
                <p><strong><?= _l('membership_email') ?>:</strong> ${member.email || '-'}</p>
                <p><strong><?= _l('membership_election') ?>:</strong> ${member.election_title || '-'}</p>
                <p><strong><?= _l('membership_term') ?>:</strong>
                    ${member.term_start ? <?= _dt('member.term_start') ?> : '-'} -
                    ${member.term_end ? <?= _dt('member.term_end') ?> : '<?= _l('membership_present') ?>'}
                </p>
                <p><strong><?= _l('membership_status') ?>:</strong> ${ucfirst(member.status || '')}</p>
                ${member.manifesto ? `<p><strong><?= _l('membership_manifesto') ?>:</strong> ${member.manifesto}</p>` : ''}
            `);
        } else {
            $('#boardMemberModalBody').html('<p class="text-danger">' + response.message + '</p>');
        }
    });
}

// Function to load committee details in modal
function showCommitteeDetails(committeeId) {
    $('#committeeModalLabel').text('<?= _l('membership_loading') ?>...');
    $('#committeeModalBody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');
    $('#committeeModal').modal('show');

    $.get("<?= admin_url('membership/ajax_get_committee') ?>/" + committeeId, function(response) {
        if (response.success) {
            var committee = response.committee;
            $('#committeeModalLabel').text(committee.name || '<?= _l('membership_committee') ?>');
            $('#committeeModalBody').html(`
                <p><strong><?= _l('membership_committee_category') ?>:</strong> ${committee.category_name || '-'}</p>
                <p><strong><?= _l('membership_committee_description') ?>:</strong> ${committee.description || '-'}</p>
                <p><strong><?= _l('membership_status') ?>:</strong> ${ucfirst(committee.status || '')}</p>
            `);
        } else {
            $('#committeeModalBody').html('<p class="text-danger">' + response.message + '</p>');
        }
    });
}

// Function to load election results via AJAX
function loadElectionResults(electionId) {
    $.get("<?= admin_url('membership/ajax_get_election_results') ?>/" + electionId, function(response) {
        if (response.success) {
            $('#election_results_container').html(response.results_html);
        } else {
            $('#election_results_container').html('<p class="text-danger">' + response.message + '</p>');
        }
    }).fail(function() {
        $('#election_results_container').html('<p class="text-danger"><?= _l('membership_error_loading_results') ?></p>');
    });
}

// Function to load vote list via AJAX
function loadVoteList(electionId) {
    $.get("<?= admin_url('membership/ajax_get_votes') ?>/" + electionId, function(response) {
        if (response.success) {
            $('#vote_list_container').html(response.votes_html);
        } else {
            $('#vote_list_container').html('<p class="text-danger">' + response.message + '</p>');
        }
    }).fail(function() {
        $('#vote_list_container').html('<p class="text-danger"><?= _l('membership_error_loading_votes') ?></p>');
    });
}

// Function to load position form for editing
function editPosition(positionId) {
    $('#positionFormTitle').text('<?= _l('membership_edit_position') ?>');
    $('#positionId').val(positionId);

    $.get("<?= admin_url('membership/ajax_get_position') ?>/" + positionId, function(response) {
        if (response.success) {
            var position = response.position;
            $('#positionName').val(position.name || '');
            $('#positionDescription').val(position.description || '');
            $('#positionModal').modal('show');
        } else {
            alert_float('danger', response.message);
        }
    });
}

// Function to load member form for editing
function editMember(memberId) {
    $('#memberFormTitle').text('<?= _l('membership_edit_member') ?>');
    $('#memberId').val(memberId);

    $.get("<?= admin_url('membership/ajax_get_member') ?>/" + memberId, function(response) {
        if (response.success) {
            var member = response.member;
            $('#memberFirstname').val(member.firstname || '');
            $('#memberLastname').val(member.lastname || '');
            $('#memberEmail').val(member.email || '');
            $('#memberPhone').val(member.phone || '');
            // Add other fields as needed
            $('#memberModal').modal('show');
        } else {
            alert_float('danger', response.message);
        }
    });
}

// Initialize modals
$('#memberModal').on('hidden.bs.modal', function () {
    $('#memberId').val('');
    $('#memberModalLabel').text('<?= _l('membership_member_details') ?>');
});

$('#positionModal').on('hidden.bs.modal', function () {
    $('#positionId').val('');
    $('#positionModalLabel').text('<?= _l('membership_position_details') ?>');
});

$('#boardMemberModal').on('hidden.bs.modal', function () {
    $('#boardMemberModalLabel').text('<?= _l('membership_board_member_details') ?>');
});

$('#committeeModal').on('hidden.bs.modal', function () {
    $('#committeeModalLabel').text('<?= _l('membership_committee_details') ?>');
});

// Helper function for ucfirst (since we can't use PHP in JS)
function ucfirst(str) {
    if (!str) return str;
    return str.charAt(0).toUpperCase() + str.slice(1);
}