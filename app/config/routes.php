<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
/**
 * ------------------------------------------------------------------
 * LavaLust - an opensource lightweight PHP MVC Framework
 * ------------------------------------------------------------------
 *
 * MIT License
 *
 * Copyright (c) 2020 Ronald M. Marasigan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package LavaLust
 * @author Ronald M. Marasigan <ronald.marasigan@yahoo.com>
 * @since Version 1
 * @link https://github.com/ronmarasigan/LavaLust
 * @license https://opensource.org/licenses/MIT MIT License
 */

/*
| -------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------
| Here is where you can register web routes for your application.
|
*/
/** @var object $router **/

// =============================================================================
// AUTH (login / logout) - pareho sa prototype: Admin & Staff portal
// =============================================================================
$router->get('/',  'Auth::login');
$router->get('/login',  'Auth::login');
$router->post('/login', 'Auth::doLogin');
$router->get('/logout', 'Auth::logout');

// =============================================================================
// ADMIN routes (role: admin) - buong access
// =============================================================================
$router->get('/admin',                    'Admin::dashboard');
$router->get('/admin/members',            'Admin::members');
$router->post('/admin/members/store',     'Admin::storeMember');
$router->get('/admin/members/edit/{id}',  'Admin::editMember');
$router->post('/admin/members/update/{id}','Admin::updateMember');
$router->post('/admin/members/delete/{id}','Admin::deleteMember');
$router->get('/admin/daily-logs',         'Admin::dailyLogs');
$router->get('/admin/expenses',           'Admin::expenses');
$router->post('/admin/expenses/store',    'Admin::storeExpense');
$router->get('/admin/expenses/edit/{id}', 'Admin::editExpense');
$router->post('/admin/expenses/update/{id}','Admin::updateExpense');
$router->post('/admin/expenses/delete/{id}','Admin::deleteExpense');

// =============================================================================
// USER MANAGEMENT (admin only) - add, edit, view, delete staff users
// =============================================================================
$router->get('/admin/users',            'Admin::users');
$router->post('/admin/users/store',     'Admin::storeUser');
$router->get('/admin/users/edit/{id}',  'Admin::editUser');
$router->post('/admin/users/update/{id}','Admin::updateUser');
$router->post('/admin/users/delete/{id}','Admin::deleteUser');

// =============================================================================
// STAFF routes (role: staff) - delete disabled sa members & expenses
// Calapan branch (green + black) - existing interface
// =============================================================================
$router->get('/staff',                    'Staff::dashboard');
$router->get('/staff/todays-logs',        'Staff::todaysLogs');
$router->post('/staff/checkin',           'Staff::checkin');
$router->get('/staff/daily-logs/edit/{id}','Staff::editDailyLog');
$router->post('/staff/daily-logs/update/{id}','Staff::updateDailyLog');
$router->get('/staff/members',            'Staff::members');
$router->post('/staff/members/store',     'Staff::storeMember');
$router->get('/staff/members/edit/{id}',  'Staff::editMember');
$router->post('/staff/members/update/{id}','Staff::updateMember');
$router->get('/staff/expenses',           'Staff::expenses');
$router->post('/staff/expenses/store',    'Staff::storeExpense');
$router->get('/staff/expenses/edit/{id}', 'Staff::editExpense');
$router->post('/staff/expenses/update/{id}','Staff::updateExpense');

// =============================================================================
// STAFF PINAMALAYAN routes (role: staff, branch_id=2) - yellow + black
// =============================================================================
$router->get('/staff-pina',                    'StaffPinamalayan::dashboard');
$router->get('/staff-pina/todays-logs',        'StaffPinamalayan::todaysLogs');
$router->post('/staff-pina/checkin',           'StaffPinamalayan::checkin');
$router->get('/staff-pina/daily-logs/edit/{id}','StaffPinamalayan::editDailyLog');
$router->post('/staff-pina/daily-logs/update/{id}','StaffPinamalayan::updateDailyLog');
$router->get('/staff-pina/members',            'StaffPinamalayan::members');
$router->post('/staff-pina/members/store',     'StaffPinamalayan::storeMember');
$router->get('/staff-pina/members/edit/{id}',  'StaffPinamalayan::editMember');
$router->post('/staff-pina/members/update/{id}','StaffPinamalayan::updateMember');
$router->get('/staff-pina/expenses',           'StaffPinamalayan::expenses');
$router->post('/staff-pina/expenses/store',    'StaffPinamalayan::storeExpense');
$router->get('/staff-pina/expenses/edit/{id}', 'StaffPinamalayan::editExpense');
$router->post('/staff-pina/expenses/update/{id}','StaffPinamalayan::updateExpense');

// =============================================================================
// STAFF VICTORIA routes (role: staff, branch_id=3) - red + black
// =============================================================================
$router->get('/staff-vic',                    'StaffVictoria::dashboard');
$router->get('/staff-vic/todays-logs',        'StaffVictoria::todaysLogs');
$router->post('/staff-vic/checkin',           'StaffVictoria::checkin');
$router->get('/staff-vic/daily-logs/edit/{id}','StaffVictoria::editDailyLog');
$router->post('/staff-vic/daily-logs/update/{id}','StaffVictoria::updateDailyLog');
$router->get('/staff-vic/members',            'StaffVictoria::members');
$router->post('/staff-vic/members/store',     'StaffVictoria::storeMember');
$router->get('/staff-vic/members/edit/{id}',  'StaffVictoria::editMember');
$router->post('/staff-vic/members/update/{id}','StaffVictoria::updateMember');
$router->get('/staff-vic/expenses',           'StaffVictoria::expenses');
$router->post('/staff-vic/expenses/store',    'StaffVictoria::storeExpense');
$router->get('/staff-vic/expenses/edit/{id}', 'StaffVictoria::editExpense');
$router->post('/staff-vic/expenses/update/{id}','StaffVictoria::updateExpense');

// =============================================================================
// STAFF BARCENAGA routes (role: staff, branch_id=4) - white + black
// =============================================================================
$router->get('/staff-barc',                    'StaffBarcenaga::dashboard');
$router->get('/staff-barc/todays-logs',        'StaffBarcenaga::todaysLogs');
$router->post('/staff-barc/checkin',           'StaffBarcenaga::checkin');
$router->get('/staff-barc/daily-logs/edit/{id}','StaffBarcenaga::editDailyLog');
$router->post('/staff-barc/daily-logs/update/{id}','StaffBarcenaga::updateDailyLog');
$router->get('/staff-barc/members',            'StaffBarcenaga::members');
$router->post('/staff-barc/members/store',     'StaffBarcenaga::storeMember');
$router->get('/staff-barc/members/edit/{id}',  'StaffBarcenaga::editMember');
$router->post('/staff-barc/members/update/{id}','StaffBarcenaga::updateMember');
$router->get('/staff-barc/expenses',           'StaffBarcenaga::expenses');
$router->post('/staff-barc/expenses/store',    'StaffBarcenaga::storeExpense');
$router->get('/staff-barc/expenses/edit/{id}', 'StaffBarcenaga::editExpense');
$router->post('/staff-barc/expenses/update/{id}','StaffBarcenaga::updateExpense');

// =============================================================================
// ADMIN BRANCH MANAGEMENT routes (role: admin)
// =============================================================================
$router->get('/admin/branches',            'Admin::branches');
$router->post('/admin/branches/store',     'Admin::storeBranch');
$router->get('/admin/branches/edit/{id}',  'Admin::editBranch');
$router->post('/admin/branches/update/{id}','Admin::updateBranch');
$router->post('/admin/branches/delete/{id}','Admin::deleteBranch');

// Fallback sa login kung walang match
$router->get('*', 'Auth::login');
