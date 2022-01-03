require([
    'jquery',
    'mage/translate',
    'mage/url',
    'mage/calendar'
], function ($, $t, url) {
    url.setBaseUrl(BASE_URL);
    var customurl = url.build('') + 'appointments/index/validation';
    $('#appointment_date').calendar({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        currentText: $t('Go Today'),
        closeText: $t('Close'),
        showWeek: true,
    });

    function addMinutes(timedate, time, minutes) {
        var date = new Date(new Date(timedate + ' ' + time).getTime() + minutes * 60000);
        var tempTime = ((date.getHours().toString().length == 1) ? '0' + date.getHours() : date.getHours()) + ':' +
            ((date.getMinutes().toString().length == 1) ? '0' + date.getMinutes() : date.getMinutes()) + ':' +
            ((date.getSeconds().toString().length == 1) ? '0' + date.getSeconds() : date.getSeconds());
        return tempTime;
    }

    function Convert24to12(show_time) {
        var time = show_time.split(":"),
            hour = +time[0],
            p;
        if (hour > 12) {
            hour -= 12;
            p = " " + "PM";
        } else if (hour == 12) {
            hour = hour || 12;
            p = " " + "PM";
        } else if (hour == '00') {
            hour = hour || 12;
            p = " " + "AM";
        } else {
            hour = hour || 12;
            p = " " + "AM";
        }
        return hour + ":" + time[1] + p;
    }

    function getCovert12to24(datetime, amPmString) {
        var d = new Date(datetime + " " + amPmString);
        return d.getHours() + ':' + d.getMinutes();
    }

    function getTimedifference(orignaldate, appdate) {
        var startactualtime = new Date(orignaldate);
        var endactualtime = new Date(appdate);
        var diff = endactualtime - startactualtime;
        var diffSeconds = diff / 1000;
        var HH = Math.floor(diffSeconds / 3600);
        var MM = Math.floor(diffSeconds % 3600) / 60;
        var formatted = ((HH < 10) ? ("0" + HH) : HH) + ":" + ((MM < 10) ? ("0" + MM) : MM);
        return formatted;
    }

    $("#appointment_branch").on('change', function () {
        var appointment_time = $('#appointment_time').find('option:selected').attr('id');
        $('#appointment_time').children().remove();
        $('#appointment_time').append(`<option id="" value="">Please Choose</option>`);
        $('#appointment_date').val("");
        if (appointment_time != 0) {
            $('#appointment_time').children().remove();
            $('#appointment_time').append(`<option id="0" value="">Please Choose</option>`);
        }
        $('#diff_error').hide();
    });
    $("#appointment_date").change(function () {
        var appointment_time = $('#appointment_time').find('option:selected').attr('id');
        var schedule = $('#appointment_branch').find('option:selected').attr('id');
        if (schedule != 0) {
            var appointment_date = $('#appointment_date').val();
            var branch = $('#appointment_branch').val();
            if (appointment_date != '') {
                var days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                var d = new Date(appointment_date);
                var dayName = days[d.getDay()];
                $.ajax({
                    url: customurl,
                    dataType: 'json',
                    showLoader: true,
                    method: 'POST',
                    data: {
                        schedule: schedule,
                        dayName: dayName,
                        appointment_date: appointment_date,
                        branch: branch,
                    },
                    success: function (response) {
                        var start_time = response.from + ':' + '00';
                        var break_start_time = response.break_from + ':' + '00';
                        var break_end_time = response.break_to + ':' + '00';
                        var end_time = response.to + ':' + '00';
                        var interval = response.slot_duration;
                        var timeslots_before_break = [start_time];
                        var timeslots_after_break = [break_end_time];
                        var new_timeslots = [start_time];
                        var status = response.status;
                        var hide_time = response.hide_time.slice(0, -1);
                        var hide_time2 = hide_time.split(',');
                        //console.log(hide_time2[0]);
                        //console.log(hide_time2[1]);
                        if (status == 1) {
                            /*while (start_time != break_start_time) {
                                start_time = addMinutes(appointment_date, start_time, interval);
                                timeslots_before_break.push(start_time);
                            }
                            timeslots_before_break.pop();

                            while (break_end_time != end_time) {
                                break_end_time = addMinutes(appointment_date, break_end_time, interval);
                                timeslots_after_break.push(break_end_time);
                            }
                            timeslots_after_break.pop();*/
                            //console.log(start_time);
                            //console.log(end_time);
                            while (start_time != end_time) {
                                start_time = addMinutes(appointment_date, start_time, interval);
                                new_timeslots.push(start_time);
                            }
                            new_timeslots.pop();
                            var timeslot = [new_timeslots];
                            var finaltimeslot = [].concat.apply([], timeslot);
                            var len = finaltimeslot.length;
                            $('#appointment_time').children().remove();
                            $('#appointment_time').append(`<option id="" value="">Please Choose</option>`);
                            for (var i = 0; i < len; i++) {
                                var finaltime_slot_show = Convert24to12(finaltimeslot[i]);
                                var timeslot_id = 'time_slot_' + finaltimeslot[i];
                                $('#appointment_time').append(`<option id="${timeslot_id}" value="${finaltime_slot_show}">
                                               ${finaltime_slot_show}
                                                </option>`);
                            }
                            if (hide_time2[0] != "") {
                                for (var i = 0; i < hide_time2.length; i++) {
                                    $("#appointment_time").children('[value="' + hide_time2[i] + '"]').hide();
                                }
                            }
                            $('#closed_day').hide();
                        } else {
                            $('#appointment_time').children().remove();
                            $('#appointment_time').append(`<option id="" value="">Please Choose</option>`);
                            $('#closed_day').show();
                        }
                    }
                });
            }
            $('#branch_error').hide();
        } else {
            $('#branch_error').show();
            $('#time_error').hide();
        }
        $('#diff_error').hide();
        $('#day_error').hide();
    });
    $("#appointment_time").on('click', function () {
        var schedule = $('#appointment_branch').find('option:selected').attr('id');
        var appointment_date = $('#appointment_date').val();
        if (schedule != 0) {
            $('#branch_error').hide();
            if (appointment_date != 0) {
                $('#day_error').hide();
            } else {
                $('#day_error').show();
            }
        } else {
            $('#branch_error').show();
        }
    });
    $('#appointment_form').on('submit', function () {
        var schedule = $('#appointment_branch').find('option:selected').attr('id');
        if (schedule != 0) {
            $('#branch_error').hide();
            var appointment_date = $('#appointment_date').val();
            if (appointment_date == '') {
                $('#day_error').show();
                return false;
            } else {
                $('#day_error').hide();
                var appointment_time = $('#appointment_time').find('option:selected').attr('id');
                if (appointment_time == 0) {
                    $('#time_error').show();
                    return false;
                } else {
                    $('#time_error').hide();
                    var current_date = new Date();
                    var start_actual_time = (current_date.getMonth() + 1) + "/" + current_date.getDate() + "/" + current_date.getFullYear() + ' ' + current_date.getHours() + ':' + current_date.getMinutes() + ':' + current_date.getSeconds();
                    var selected_date = $("#appointment_date").val();
                    var selected_time = $("#appointment_time").val();
                    var end_time = getCovert12to24(selected_date, selected_time);
                    var end_actual_time = selected_date + ' ' + end_time + ':' + '00';
                    var timeDifference = getTimedifference(start_actual_time, end_actual_time);
                    var set_timeDifference = timeDifference.replace(/[-:]/g, ".");
                    var actual_timeDifference = set_timeDifference.substring(0, 4);
                    if (actual_timeDifference >= 24) {
                        return true;
                    } else {
                        $('#diff_error').show();
                        return false;
                    }
                }
            }
        } else {
            $('#branch_error').show();
            return false;
        }
    });
});