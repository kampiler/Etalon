@echo off
title Etalon

set path=C:\UTILZ\php;C:\UTILZ;%PATH%
cd C:\BAT

if .%1==.START_ETALON goto START_ETALON
if .%1==.SETUP_ETALON goto SETUP_ETALON

  echo �㦭� ��ࠬ���� - etalon.cmd [ SETUP_ETALON or START_ETALON ]
  goto ENND

:SETUP_ETALON
  echo ����襭 �ਯ� ���������� �⠫��� - ��४��� �㤥� ��饭� � ��������� ������ (��ࢠ�� - CTRL+C)
  pause
  php.exe C:\BAT\etalon_setup.php 
  goto ENND

:START_ETALON
  php.exe C:\BAT\etalon.php
  goto ENND



:ENND