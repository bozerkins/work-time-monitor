<!doctype html>
<html lang="en" ng-app="app">
<head>
    <meta charset="utf-8">
    <title>Personal schedule management</title>
    <meta content='width=device-width, initial-scale=1' name='viewport'/>
    <link href="resource/bootstrap.min.css" rel="stylesheet">
    <script src="resource/jquery-2.2.4.min.js"></script>
    <script src="resource/bootstrap.min.js"></script>
    <script type="text/javascript" src="resource/angular.min.js"></script>

    <script>
        angular.module('app', [])
            .controller('ScheduleController', ['$scope', '$http', function($scope, $http) {
                $scope.session = null;
                $scope.fireEvent = function(eventName) {
                    $http.get('/api.php?' + $.param({event: 'event_start', event_name: eventName})).then(function(response) {
                        $scope.sessionFetch();
                    });
                };
                $scope.sessionStart = function() {
                    $http.get('/api.php?' + $.param({event: 'session_start'})).then(function(response) {
                        $scope.sessionFetch();
                    });
                };
                $scope.sessionEnd = function() {
                    $http.get('/api.php?' + $.param({event: 'session_stop'})).then(function(response) {
                        $scope.sessionFetch();
                    });
                };
                $scope.sessionFetch = function(sessionId) {
                    $http.get('/api.php?' + $.param({event: 'get_current_session', session_id: sessionId})).then(function(response) {
                        $scope.session = response.data.result;
                    });
                };
                $scope.sessions = null;
                $scope.sessionListFetch = function() {
                    $http.get('/api.php?' + $.param({event: 'get_session_list'})).then(function(response) {
                        $scope.sessions = response.data.result;
                    });
                };
            }]);
    </script>

</head>
<body ng-controller="ScheduleController" ng-init="sessionFetch();sessionListFetch();">
    <div class="container" ng-if="!sessionSelected">
        <div class="row">
            <h2 class="text-center" style="padding-bottom: 30px;">Workday management</h2>
            <div class="col-xs-6 text-right">
                <div ng-if="session">Session started at: <b>{{ session.time_start }}</b></div>
                <div ng-if="session">Events fired: <b>{{ session.events.length }}</b></div>
                <div ng-if="!session">No session started</div>
            </div>
            <div class="col-xs-6">
                <input type="button" class="btn btn-primary" value="Workday Start" ng-if="!session" ng-click="sessionStart()"/>
                <input type="button" class="btn btn-primary" value="Workday Stop" ng-if="session" ng-click="sessionEnd()"/>
            </div>
        </div>
        <hr>
        <div class="row">
            <h2 class="text-center" style="padding-bottom: 30px;">Event management</h2>
            <div class="col-xs-12 text-center">
                <input type="button" class="btn btn-default" style="margin: 10px;" value="Computer work" ng-click="fireEvent('Computer work')" />
                <input type="button" class="btn btn-default" style="margin: 10px;" value="Communication work" ng-click="fireEvent('Communication work')" />
                <input type="button" class="btn btn-default" style="margin: 10px;" value="Coffee break" ng-click="fireEvent('Coffee break')" />
                <input type="button" class="btn btn-default" style="margin: 10px;" value="Smoke break" ng-click="fireEvent('Smoke break')" />
                <input type="button" class="btn btn-default" style="margin: 10px;" value="Dinner break" ng-click="fireEvent('Dinner break')" />
                <hr>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <h2 class="text-center" style="padding-bottom: 30px;">Event Summary</h2>
            <div class="col-xs-12" style="padding-bottom: 10px;">
                Select session for summary:
                <select name="repeatSelect" ng-model="sessionSelected" ng-change="sessionFetch(sessionSelected)">
                    <option value="">Current session</option>
                    <option ng-repeat="option in sessions" value="{{option.id}}">{{option.work_date + '-' + option.id}}</option>
                </select>
            </div>
            <div class="col-xs-12">
                <table ng-if="session.events.length" class="table table-bordered">
                    <tr>
                        <th style="width: 40%;">Event name</th>
                        <th style="width: 20%;">Started</th>
                        <th style="width: 20%;">Ended</th>
                        <th style="width: 20%;">Total</th>
                    </tr>
                    <tr ng-repeat="(key, event) in session.events" ng-class="{ active: event.event_time_end === null }">
                        <td>{{ event.event_name }}</td>
                        <td>{{ event.event_time_start }}</td>
                        <td>{{ event.event_time_end }}</td>
                        <td>{{ event.event_time_total }}</td>
                    </tr>
                    <tr>
                        <th>Summary:</th>
                        <td colspan="3">
                            Worked: <b>{{ session.summary.work }}</b><br>
                            Break: <b>{{ session.summary.break }}</b><br>
                            Total: <b>{{ session.summary.total }}</b><br>
                        </td>
                    </tr>

                </table>
                <div ng-if="!session.events.length">
                    No events fired yet
                </div>
            </div>
        </div>
    </div>
</body>
</html>